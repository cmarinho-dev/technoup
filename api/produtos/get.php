<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$id = isset($_GET['id']) && $_GET['id'] !== '' ? (int)$_GET['id'] : null;
$lojaId = isset($_GET['loja_id']) && $_GET['loja_id'] !== '' ? (int)$_GET['loja_id'] : null;

// Monta consulta conforme parâmetros
if ($id !== null) {
    $stmt = $conexao->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->bind_param('i', $id);
} elseif ($lojaId !== null) {
    $stmt = $conexao->prepare("SELECT * FROM produto WHERE loja_id = ? ORDER BY criado_em DESC");
    $stmt->bind_param('i', $lojaId);
} else {
    $stmt = $conexao->prepare("SELECT * FROM produto ORDER BY desconto DESC, criado_em DESC");
}

$stmt->execute();
$resultado = $stmt->get_result();

$produtos = [];
while ($produto = $resultado->fetch_assoc()) {
    // Busca a imagem principal do produto
    $stmtImg = $conexao->prepare("SELECT * FROM imagem_produto WHERE produto_id = ? LIMIT 1");
    $stmtImg->bind_param('i', $produto['id']);
    $stmtImg->execute();
    $resultadoImg = $stmtImg->get_result();
    $stmtImg->close();
    $produto['imagem'] = $resultadoImg->num_rows > 0 ? $resultadoImg->fetch_assoc() : null;

    // Busca o nome da loja
    $stmtLoja = $conexao->prepare("SELECT nome_loja FROM loja WHERE id = ?");
    $stmtLoja->bind_param('i', $produto['loja_id']);
    $stmtLoja->execute();
    $resultadoLoja = $stmtLoja->get_result();
    $stmtLoja->close();
    $produto['nome_loja'] = $resultadoLoja->num_rows > 0 ? $resultadoLoja->fetch_assoc()['nome_loja'] : 'Loja parceira';

    $produtos[] = $produto;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $produtos);
