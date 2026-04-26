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


// Consulta para obter todas as contas
// A senha não deve ser retornada, mas como é apenas para testes, deixei para facilitar a visualização
$sql = "
    SELECT
        conta.senha,
        conta.id,
        conta.nome,
        conta.email,
        conta.tipo,
        conta.ativo,
        conta.criado_em
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
