<?php
include_once '../funcoes.php';

session_start();

if (isset($_SESSION['usuario'])) {
    respostaJson('ok', '', [
        'usuario' => $_SESSION['usuario'],
        'loja'    => $_SESSION['loja'] ?? null
    ]);
} else {
    respostaJson('nok', 'Sessão não iniciada.');
}
