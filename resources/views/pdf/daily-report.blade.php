<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Citas</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 24px; }
        .stats { margin-bottom: 20px; background: #f3f4f6; padding: 10px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4f46e5; color: white; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .footer { margin-top: 40px; text-align: center; color: #777; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Citas Médicas</h1>
        <p>Fecha del Reporte: {{ date('d/m/Y', strtotime($date)) }}</p>
    </div>

    <div class="stats">
        <strong>Total de Citas para hoy:</strong> {{ $appointments->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Horario</th>
                <th width="30%">Paciente</th>
                <th width="30%">Doctor</th>
                <th width="25%">Especialidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td>{{ date('H:i', strtotime($appointment->start_time)) }} - {{ date('H:i', strtotime($appointment->end_time)) }}</td>
                <td>{{ $appointment->patient->user->name ?? 'Desconocido' }}</td>
                <td>Dr(a). {{ $appointment->doctor->user->name ?? 'Desconocido' }}</td>
                <td>{{ $appointment->doctor->speciality->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado automáticamente a las {{ now()->format('H:i:s') }}</p>
    </div>
</body>
</html>
