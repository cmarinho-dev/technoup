<?php
session_start();
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'lojista') {
    $retorno = [
        'status' => 'ok', // ok - nok
        'mensagem' => '', // mensagem que envio para o front
        'data' => []
    ];
    session_write_close();
} else {
    $retorno = [
        'status' => 'nok', // ok - nok
        'mensagem' => 'Você precisa estar logado para acessar esta página.', // mensagem que envio para o front
        'data' => []
    ];
    session_write_close();
    header("Location: ../catalogo/");
    exit();
}