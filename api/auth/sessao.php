<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['usuario'])) {
    echo json_encode([
        'status'   => 'ok',
        'mensagem' => '',
        'data'     => [
            'usuario' => $_SESSION['usuario'],
            'loja'    => $_SESSION['loja'] ?? null
        ]
    ]);
} else {
    echo json_encode([
        'status'   => 'nok',
        'mensagem' => 'Sessão não iniciada.',
        'data'     => []
    ]);
}
