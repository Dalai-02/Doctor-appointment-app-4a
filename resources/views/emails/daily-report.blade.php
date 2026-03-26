<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #4f46e5;">Reporte de Citas Diarias</h2>
    <p>Hola Administrador,</p>
    <p>Se adjunta a este correo el reporte completo en formato PDF con el consolidado de las citas médicas programadas para el transcurso de hoy, <strong>{{ date('d de M, Y', strtotime($date)) }}</strong>.</p>
    <p>Total de citas agendadas: <strong>{{ $appointments->count() }}</strong></p>
    <br>
    <p><small>Este reporte fue generado de manera automática.</small></p>
</body>
</html>
