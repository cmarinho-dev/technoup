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
    $stmt = $conexao->prepare("SELECT
        produto.*,
        loja.nome_loja,
        COALESCE(notas.media_atendimento, 0) AS media_atendimento,
        COALESCE(notas.total_avaliacoes_atendimento, 0) AS total_avaliacoes_atendimento
    FROM produto
    JOIN loja ON produto.loja_id = loja.id
    JOIN conta ON loja.conta_id = conta.id
    LEFT JOIN (
        SELECT
            loja_id,
            ROUND(AVG(nota), 1) AS media_atendimento,
            COUNT(id) AS total_avaliacoes_atendimento
        FROM avaliacao_atendimento
        GROUP BY loja_id
    ) notas ON notas.loja_id = loja.id
    WHERE conta.tipo = 'lojista'
    AND conta.ativo = 1
    AND produto.id = ?");
    $stmt->bind_param('i', $id);
} elseif ($lojaId !== null) {
    $stmt = $conexao->prepare("SELECT
        produto.*,
        loja.nome_loja,
        COALESCE(notas.media_atendimento, 0) AS media_atendimento,
        COALESCE(notas.total_avaliacoes_atendimento, 0) AS total_avaliacoes_atendimento
    FROM produto
    JOIN loja ON produto.loja_id = loja.id
    JOIN conta ON loja.conta_id = conta.id
    LEFT JOIN (
        SELECT
            loja_id,
            ROUND(AVG(nota), 1) AS media_atendimento,
            COUNT(id) AS total_avaliacoes_atendimento
        FROM avaliacao_atendimento
        GROUP BY loja_id
    ) notas ON notas.loja_id = loja.id
    WHERE conta.tipo = 'lojista'
    AND conta.ativo = 1
    AND produto.loja_id = ? 
    ORDER BY criado_em DESC");
    $stmt->bind_param('i', $lojaId);
} else {
    $stmt = $conexao->prepare("SELECT
        produto.*,
        loja.nome_loja,
        COALESCE(notas.media_atendimento, 0) AS media_atendimento,
        COALESCE(notas.total_avaliacoes_atendimento, 0) AS total_avaliacoes_atendimento
    FROM produto
    JOIN loja ON produto.loja_id = loja.id
    JOIN conta ON loja.conta_id = conta.id
    LEFT JOIN (
        SELECT
            loja_id,
            ROUND(AVG(nota), 1) AS media_atendimento,
            COUNT(id) AS total_avaliacoes_atendimento
        FROM avaliacao_atendimento
        GROUP BY loja_id
    ) notas ON notas.loja_id = loja.id
    WHERE conta.tipo = 'lojista'
    AND conta.ativo = 1
    ORDER BY desconto DESC, criado_em DESC");
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

    $produtos[] = $produto;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $produtos);
