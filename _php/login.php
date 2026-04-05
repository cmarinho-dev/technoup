<?php
include_once '../_php/crud.php';
$email = $_POST["email"];
$senha = $_POST["senha"];

$resultado_contas = ler('conta', $email, 'email');
$conta = [];

if ($resultado_contas->num_rows > 0) {
    $usuario_encontrado = $resultado_contas->fetch_assoc();
    var_dump($usuario_encontrado);
    if ($usuario_encontrado['senha'] === $senha) {
        $usuario_encontrado['senha'] = null; // Remove a senha para seguranç
        session_start();

        $_SESSION['usuario'] = $usuario_encontrado;

        if ($_SESSION['usuario']['tipo'] === 'lojista') {
            if (adicionarSessaoLoja()) {
                header('Location: ../lojas/');
                exit;
            } else {
                header('Location: ../lojas/cadastro_loja.php');
                exit;
            }
        }

        header('Location: ../catalogo/');
        exit;
    }
}
header('Location: .');
exit;


function adicionarSessaoLoja()
{
    $resultado_loja = ler('loja', $_SESSION['usuario']['id'], 'conta_id');
    if ($resultado_loja->num_rows > 0) {
        $_SESSION['loja'] = $resultado_loja->fetch_assoc();
        return true;
    }
    return false;
}