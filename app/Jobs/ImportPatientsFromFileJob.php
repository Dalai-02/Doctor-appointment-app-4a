<?php

namespace App\Jobs;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Throwable;

class ImportPatientsFromFileJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $filePath,
        public ?int $triggeredBy = null,
    ) {
    }

    public function handle(): void
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            Log::warning('Patient import file not found.', ['file' => $this->filePath]);
            return;
        }

        $absolutePath = Storage::disk('local')->path($this->filePath);
        $reader = $this->makeReader($absolutePath);

        $imported = 0;
        $skipped = 0;

        $reader->open($absolutePath);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $header = [];

                foreach ($sheet->getRowIterator() as $index => $row) {
                    $cells = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row->toArray());

                    if ($index === 1) {
                        $header = $this->normalizeHeader($cells);
                        continue;
                    }

                    $rowData = $this->mapRow($header, $cells);

                    if ($this->isRowEmpty($rowData)) {
                        continue;
                    }

                    if (!$this->hasRequiredFields($rowData)) {
                        $skipped++;
                        continue;
                    }

                    $email = Str::lower((string) $rowData['email']);
                    $idNumber = (string) $rowData['id_number'];

                    $user = User::query()->where('email', $email)->first();
                    $idInUse = User::query()
                        ->where('id_number', $idNumber)
                        ->when($user, fn ($query) => $query->where('id', '!=', $user->id))
                        ->exists();

                    if ($idInUse) {
                        $skipped++;
                        continue;
                    }

                    if (!$user) {
                        $user = new User();
                        $user->email = $email;
                        $user->password = Hash::make(Str::password(12));
                    }

                    $user->name = (string) $rowData['name'];
                    $user->id_number = $idNumber;
                    $user->phone = (string) $rowData['phone'];
                    $user->address = (string) $rowData['address'];
                    $user->save();

                    if (method_exists($user, 'assignRole') && !$user->hasRole('Paciente') && \Spatie\Permission\Models\Role::query()->where('name', 'Paciente')->exists()) {
                        $user->assignRole('Paciente');
                    }

                    $patient = Patient::query()->firstOrNew(['user_id' => $user->id]);
                    $patient->blood_type_id = $this->resolveBloodTypeId($rowData['blood_type'] ?? null);
                    $patient->allergies = $rowData['allergies'] ?? null;
                    $patient->chronic_conditions = $rowData['chronic_conditions'] ?? null;
                    $patient->surgical_history = $rowData['surgical_history'] ?? null;
                    $patient->family_history = $rowData['family_history'] ?? null;
                    $patient->observations = $rowData['observations'] ?? null;
                    $patient->emergency_contact_name = $rowData['emergency_contact_name'] ?? null;
                    $patient->emergency_contact_phone = $rowData['emergency_contact_phone'] ?? null;
                    $patient->emergency_contact_relationship = $rowData['emergency_contact_relationship'] ?? null;
                    $patient->save();

                    $imported++;
                }

                break;
            }
        } finally {
            $reader->close();
        }

        Log::info('Patient import finished.', [
            'file' => $this->filePath,
            'triggered_by' => $this->triggeredBy,
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        Storage::disk('local')->delete($this->filePath);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Patient import job failed.', [
            'file' => $this->filePath,
            'triggered_by' => $this->triggeredBy,
            'error' => $exception?->getMessage(),
        ]);
    }

    private function makeReader(string $absolutePath): CsvReader|XlsxReader
    {
        $extension = Str::lower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'csv', 'txt' => new CsvReader(),
            'xlsx' => new XlsxReader(),
            default => throw new \RuntimeException('Formato no soportado para importación masiva.'),
        };
    }

    private function normalizeHeader(array $headerRow): array
    {
        return array_map(
            fn ($value) => Str::snake(Str::lower(trim((string) $value))),
            $headerRow
        );
    }

    private function mapRow(array $header, array $cells): array
    {
        $mapped = [];

        foreach ($header as $index => $key) {
            if ($key === '') {
                continue;
            }

            $mapped[$key] = isset($cells[$index]) ? trim((string) $cells[$index]) : null;
        }

        return $mapped;
    }

    private function hasRequiredFields(array $row): bool
    {
        $required = ['name', 'email', 'id_number', 'phone', 'address'];

        foreach ($required as $field) {
            if (empty($row[$field])) {
                return false;
            }
        }

        return true;
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (!is_null($value) && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function resolveBloodTypeId(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        if (ctype_digit($value)) {
            return BloodType::query()->whereKey((int) $value)->value('id');
        }

        return BloodType::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower(trim($value))])
            ->value('id');
    }
}
