<?php

namespace App\Jobs;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Spatie\Permission\Models\Role;
use Throwable;

class ImportPatientsFromFileJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    private ?int $patientRoleId = null;

    /** @var array<string, int> */
    private array $bloodTypeMap = [];

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

        $this->warmupCaches();

        $absolutePath = Storage::disk('local')->path($this->filePath);
        $reader = $this->makeReader($absolutePath);

        if ($this->triggeredBy) {
            Cache::put("import_progress_{$this->triggeredBy}", ['processed' => 0, 'completed' => false], now()->addMinutes(60));
        }

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

                    $email = Str::lower((string) $rowData['correo']);
                    $idNumber = 'PAT-' . strtoupper(Str::random(8)); // Generated since not in CSV

                    $matchedUsers = User::query()
                        ->where('email', $email)
                        ->get();

                    $user = $matchedUsers->first();

                    if (!$user) {
                        $user = new User();
                        $user->email = $email;
                        $user->password = Hash::make(Str::password(12));
                        $user->id_number = $idNumber;
                        $user->address = 'Sin dirección'; // Default because not in CSV
                    }

                    $user->name = (string) $rowData['nombre_completo'];
                    $user->phone = (string) $rowData['telefono'];
                    $user->save();

                    if ($this->patientRoleId) {
                        $user->roles()->syncWithoutDetaching([$this->patientRoleId]);
                    }

                    $patient = Patient::query()->firstOrNew(['user_id' => $user->id]);
                    $patient->date_of_birth = !empty($rowData['fecha_nacimiento']) ? date('Y-m-d', strtotime(str_replace('/', '-', $rowData['fecha_nacimiento']))) : null;
                    $patient->blood_type_id = $this->resolveBloodTypeId($rowData['tipo_sangre'] ?? null);
                    $patient->allergies = $rowData['alergias'] ?? null;
                    $patient->chronic_conditions = $rowData['chronic_conditions'] ?? null;
                    $patient->surgical_history = $rowData['surgical_history'] ?? null;
                    $patient->family_history = $rowData['family_history'] ?? null;
                    $patient->observations = $rowData['observations'] ?? null;
                    $patient->emergency_contact_name = $rowData['emergency_contact_name'] ?? null;
                    $patient->emergency_contact_phone = $rowData['emergency_contact_phone'] ?? null;
                    $patient->emergency_contact_relationship = $rowData['emergency_contact_relationship'] ?? null;
                    $patient->save();

                    $imported++;

                    if ($this->triggeredBy && ($index - 1) % 10 === 0) {
                        Cache::put("import_progress_{$this->triggeredBy}", ['processed' => ($index - 1), 'completed' => false], now()->addMinutes(60));
                    }
                }

                if ($this->triggeredBy) {
                    Cache::put("import_progress_{$this->triggeredBy}", ['processed' => ($index - 1 ?? 0), 'completed' => true], now()->addMinutes(60));
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
        $required = ['nombre_completo', 'correo', 'telefono', 'fecha_nacimiento', 'tipo_sangre', 'alergias'];

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
            $id = (int) $value;

            return in_array($id, $this->bloodTypeMap, true) ? $id : null;
        }

        return $this->bloodTypeMap[Str::lower(trim($value))] ?? null;
    }

    private function warmupCaches(): void
    {
        $this->patientRoleId = Role::query()->where('name', 'Paciente')->value('id');

        $bloodTypes = BloodType::query()->get(['id', 'name']);

        $this->bloodTypeMap = [];

        foreach ($bloodTypes as $bloodType) {
            $this->bloodTypeMap[(string) $bloodType->id] = (int) $bloodType->id;
            $this->bloodTypeMap[Str::lower((string) $bloodType->name)] = (int) $bloodType->id;
        }
    }
}
