<?php

namespace Tests\Feature;

use App\Jobs\ImportPatientsFromFileJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientMassImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_mass_import_dispatches_background_job(): void
    {
        Queue::fake();
        Storage::fake('local');

        $user = User::factory()->create();

        $csvContent = "name,email,id_number,phone,address\n";
        $csvContent .= "Juan Perez,juan@example.com,ID-100001,9991234567,Calle Uno 123\n";

        $file = UploadedFile::fake()->createWithContent('patients.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('admin.patients.import'), [
            'patients_file' => $file,
        ]);

        $response->assertRedirect(route('admin.patients.index'));

        Queue::assertPushed(ImportPatientsFromFileJob::class);
    }
}
