<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

function buscarOuCriarChat($conexao, $usuario, $lojaId) {
    $consumidorId = (int)$usuario['id'];

    if ($usuario['tipo'] !== 'consumidor') {
        $consumidorId = 3;
    }

    $stmtLoja = $conexao->prepare("
        SELECT loja.id
        FROM loja
        JOIN conta ON conta.id = loja.conta_id
        WHERE loja.id = ? AND conta.ativo = 1
        LIMIT 1
    ");
    $stmtLoja->bind_param('i', $lojaId);
    $stmtLoja->execute();
    $loja = $stmtLoja->get_result()->fetch_assoc();
    $stmtLoja->close();

    if (!$loja) {
        respostaJson('nok', 'Loja fixa do chat não encontrada.');
    }

    $stmt = $conexao->prepare("
        SELECT id
        FROM chat_cotacao_usado
        WHERE consumidor_id = ? AND loja_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('ii', $consumidorId, $lojaId);
    $stmt->execute();
    $chat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($chat) {
        return (int)$chat['id'];
    }

    $stmt = $conexao->prepare("
        INSERT INTO chat_cotacao_usado (consumidor_id, loja_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param('ii', $consumidorId, $lojaId);
    $stmt->execute();
    $chatId = $stmt->insert_id;
    $stmt->close();

    return (int)$chatId;
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
session_write_close();

$mensagem = trim($_POST['mensagem'] ?? '');
if ($mensagem === '') {
    respostaJson('nok', 'Digite uma mensagem antes de enviar.');
}

$tamanhoMensagem = function_exists('mb_strlen') ? mb_strlen($mensagem, 'UTF-8') : strlen($mensagem);
if ($tamanhoMensagem > 1000) {
    respostaJson('nok', 'A mensagem deve ter no máximo 1000 caracteres.');
}

$lojaId = 1;
$chatId = buscarOuCriarChat($conexao, $usuario, $lojaId);
$isCliente = $usuario['tipo'] === 'consumidor' ? 0 : 1;

$stmt = $conexao->prepare("
    INSERT INTO mensagem_cotacao_usado (is_cliente, chat_id, mensagem)
    VALUES (?, ?, ?)
");
$stmt->bind_param('iis', $isCliente, $chatId, $mensagem);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível enviar a mensagem.');
}

$mensagemId = $stmt->insert_id;
$stmt->close();

$stmt = $conexao->prepare("
    SELECT id, is_cliente, chat_id, mensagem, criado_em
    FROM mensagem_cotacao_usado
    WHERE id = ?
");
$stmt->bind_param('i', $mensagemId);
$stmt->execute();
$mensagemCriada = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conexao->close();

respostaJson('ok', 'Mensagem enviada.', $mensagemCriada);
