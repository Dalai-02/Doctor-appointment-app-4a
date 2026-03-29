<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Cita</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #4f46e5; margin: 0; }
        .content { margin-top: 20px; }
        .row { margin-bottom: 15px; }
        .label { font-weight: bold; width: 120px; display: inline-block; }
        .footer { margin-top: 50px; text-align: center; color: #777; font-size: 12px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comprobante de Cita Médica</h1>
        <p>Sistema de Gestión Médica</p>
    </div>

    <div class="content">
        <div class="row">
            <span class="label">Folio de Cita:</span> #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}
        </div>
        <div class="row">
            <span class="label">Paciente:</span> {{ $appointment->patient->user->name }}
        </div>
        <div class="row">
            <span class="label">DNI/ID:</span> {{ $appointment->patient->user->id_number ?? 'N/A' }}
        </div>
        <div class="row">
            <span class="label">Fecha:</span> {{ $appointment->date }}
        </div>
        <div class="row">
            <span class="label">Horario:</span> {{ date('H:i', strtotime($appointment->start_time)) }} - {{ date('H:i', strtotime($appointment->end_time)) }}
        </div>
        <div class="row">
            <span class="label">Doctor:</span> Dr(a). {{ $appointment->doctor->user->name }}
        </div>
        <div class="row">
            <span class="label">Especialidad:</span> {{ $appointment->doctor->speciality->name ?? 'Medicina General' }}
        </div>
        <div class="row">
            <span class="label">Motivo:</span> {{ $appointment->reason }}
        </div>
    </div>

    <div class="footer">
        <p>Este documento es un comprobante automático generado el {{ now()->format('d/m/Y H:i') }}.</p>
    </div>
</body>
</html>
