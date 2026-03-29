<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentsReport;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentsReport extends Command
{
    protected $signature = 'app:send-daily-appointments-report';
    protected $description = 'Genera y envía un reporte en PDF con todas las citas del día actual al administrador';

    public function handle()
    {
        $today = now()->toDateString();
        
        // Get all appointments exactly for today
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->where('date', $today)
            ->orderBy('start_time')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No hay citas registradas para hoy ({$today}). No se enviará el reporte.");
            return;
        }

        // Try to get an admin user
        $admin = User::role('admin')->first();
        $adminEmail = $admin ? $admin->email : config('mail.from.address', 'admin@example.com');

        try {
            Mail::to($adminEmail)->send(new DailyAppointmentsReport($appointments, $today));
            $this->info("Reporte enviado exitosamente a {$adminEmail}.");
        } catch (\Exception $e) {
            Log::error('Error enviando el reporte diario de citas: ' . $e->getMessage());
            $this->error('Ocurrió un error al enviar el correo. Revisa el archivo de log.');
        }
    }
}
