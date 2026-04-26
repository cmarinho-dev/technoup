<?php
// Retorna a lista de contas de lojistas, incluindo detalhes da loja associada (um JOIN de contas e lojas)
// Requer autenticação de administrador

include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao administrador.');
}
session_write_close();

$sql = "
    SELECT
        conta.id        AS conta_id,
        conta.nome      AS nome_conta,
        conta.email,
        conta.ativo,
        conta.criado_em,
        loja.nome_loja,
        loja.telefone,
        loja.cnpj,
        loja.cidade,
        loja.estado,
        loja.logradouro,
        loja.numero
    FROM conta
    LEFT JOIN loja ON loja.conta_id = conta.id
    WHERE conta.tipo = 'lojista'
    ORDER BY conta.criado_em DESC
";

$resultado = $conexao->query($sql);
$lojistas = [];
while ($linha = $resultado->fetch_assoc()) {
    $lojistas[] = $linha;
}

$conexao->close();
respostaJson('ok', '', $lojistas);
