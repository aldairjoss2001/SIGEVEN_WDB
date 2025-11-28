<?php
require_once 'sesion.php';

header('Content-Type: application/json');

iniciar_sesion_segura();

if (esta_autenticado()) {
    $datos = obtener_datos_sesion();
    echo json_encode([
        'autenticado' => true,
        'usuario' => [
            'id' => $datos['id'],
            'tipo' => $datos['tipo'],
            'nombre' => $datos['nombre'],
            'codigo' => $datos['codigo'],
            'correo' => $datos['correo']
        ]
    ]);
} else {
    echo json_encode([
        'autenticado' => false
    ]);
}
?>
