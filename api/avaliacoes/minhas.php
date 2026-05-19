<?php
include_once '../conexao.php';
include_once '../funcoes.php';

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
        chat_cotacao_item.id AS chat_id,
        chat_cotacao_item.status AS status_chat,
        avaliacao_atendimento.id AS atendimento_avaliacao_id,
        avaliacao_atendimento.nota AS atendimento_nota,
        avaliacao_atendimento.comentario AS atendimento_comentario,
        avaliacao_atendimento.criado_em AS atendimento_avaliado_em
    FROM avaliacao_item
    JOIN loja ON loja.id = avaliacao_item.loja_id
    LEFT JOIN chat_cotacao_item ON chat_cotacao_item.avaliacao_id = avaliacao_item.id
    LEFT JOIN avaliacao_atendimento ON avaliacao_atendimento.avaliacao_id = avaliacao_item.id
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
