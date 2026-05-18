<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'consumidor') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao consumidor.');
}
$consumidorId = (int)$_SESSION['usuario']['id'];
session_write_close();

$stmt = $conexao->prepare("
    SELECT
        avaliacao_item.*,
        loja.nome_loja,
        loja.banner_img,
        chat_cotacao_item.id AS chat_id
    FROM avaliacao_item
    JOIN loja ON loja.id = avaliacao_item.loja_id
    LEFT JOIN chat_cotacao_item ON chat_cotacao_item.avaliacao_id = avaliacao_item.id
    WHERE avaliacao_item.consumidor_id = ?
    ORDER BY avaliacao_item.criado_em DESC
");
$stmt->bind_param('i', $consumidorId);
$stmt->execute();
$resultado = $stmt->get_result();

$avaliacoes = [];
while ($linha = $resultado->fetch_assoc()) {
    $avaliacoes[] = $linha;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $avaliacoes);
