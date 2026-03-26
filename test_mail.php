<?php
try { 
    \Illuminate\Support\Facades\Mail::raw("Este es un correo puramente de prueba lanzado desde la terminal de tu servidor local. Si estás leyendo esto, las credenciales SMTP de tu Gmail funcionan perfectamente con Laravel.", function($msg){ 
        $msg->to("dalaipacheco3@gmail.com")->subject("Prueba de Conexión SMTP a Gmail"); 
    }); 
    echo "\n\n=========== EXITO TOTAL. REVISA TU BANDEJA DE CORREO ===========\n\n"; 
} catch (\Exception $e) { 
    echo "\n\n============= ERROR SMTP =============\n\n" . $e->getMessage() . "\n\n"; 
}
