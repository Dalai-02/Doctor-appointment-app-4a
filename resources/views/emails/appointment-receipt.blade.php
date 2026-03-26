<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #4f46e5;">¡Hola, {{ $appointment->patient->user->name }}!</h2>
    <p>Tu cita médica ha sido agendada con éxito en nuestro sistema.</p>
    <ul>
        <li><strong>Fecha:</strong> {{ $appointment->date }}</li>
        <li><strong>Hora de inicio:</strong> {{ date('H:i', strtotime($appointment->start_time)) }}</li>
        <li><strong>Doctor:</strong> Dr(a). {{ $appointment->doctor->user->name }}</li>
        <li><strong>Motivo:</strong> {{ $appointment->reason }}</li>
    </ul>
    <p>Adjunto a este correo encontrarás tu comprobante oficial en formato PDF.</p>
    <p>¡Gracias por tu confianza!</p>
</body>
</html>
