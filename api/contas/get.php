<?php
// APENAS PARA TESTES

include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

//session_start();
//if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
//    session_write_close();
//    respostaJson('nok', 'Acesso restrito ao administrador.');
//}
//session_write_close();

$sql = "
    SELECT
        conta.email,
        conta.senha,
        conta.tipo,
        conta.ativo
    FROM conta
    ORDER BY conta.tipo DESC
";

$resultado = $conexao->query($sql);
$lojistas = [];
while ($linha = $resultado->fetch_assoc()) {
    $lojistas[] = $linha;
}

$conexao->close();
respostaJson('ok', '', $lojistas);
