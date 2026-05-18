<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

function lojaDaSessao($conexao, $usuario) {
    if ($usuario['tipo'] !== 'lojista') return null;
    if (isset($_SESSION['loja']['id'])) return (int)$_SESSION['loja']['id'];

    $contaId = (int)$usuario['id'];
    $stmt = $conexao->prepare("SELECT id FROM loja WHERE conta_id = ? LIMIT 1");
    $stmt->bind_param('i', $contaId);
    $stmt->execute();
    $loja = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $loja ? (int)$loja['id'] : null;
}

function carregarChatPermitido($conexao, $usuario, $lojaId) {
    $avaliacaoId = isset($_POST['avaliacao_id']) ? (int)$_POST['avaliacao_id'] : 0;
    $chatId = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;

    if ($chatId <= 0 && $avaliacaoId <= 0) {
        respostaJson('nok', 'Informe uma avaliação aceita ou um chat.');
    }

    $params = [];
    $types = '';
    $where = $chatId > 0 ? 'chat_cotacao_usado.id = ?' : 'avaliacao_peca.id = ?';
    $params[] = $chatId > 0 ? $chatId : $avaliacaoId;
    $types .= 'i';

    if ($usuario['tipo'] === 'consumidor') {
        $where .= ' AND avaliacao_peca.consumidor_id = ?';
        $params[] = (int)$usuario['id'];
        $types .= 'i';
    } elseif ($usuario['tipo'] === 'lojista') {
        if (!$lojaId) respostaJson('nok', 'Loja não encontrada na sessão.');
        $where .= ' AND avaliacao_peca.loja_id = ?';
        $params[] = $lojaId;
        $types .= 'i';
    } elseif ($usuario['tipo'] !== 'administrador') {
        respostaJson('nok', 'Acesso restrito.');
    }

    $sql = "
        SELECT chat_cotacao_usado.id, chat_cotacao_usado.status AS chat_status, avaliacao_peca.status AS avaliacao_status
        FROM chat_cotacao_usado
        JOIN avaliacao_peca ON avaliacao_peca.id = chat_cotacao_usado.avaliacao_id
        WHERE {$where}
        LIMIT 1
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $chat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$chat || $chat['avaliacao_status'] !== 'aceita') {
        respostaJson('nok', 'Este chat ainda não foi liberado pela loja.');
    }

    if ($chat['chat_status'] !== 'aberto') {
        respostaJson('nok', 'Este chat foi fechado.');
    }

    return (int)$chat['id'];
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
$lojaId = lojaDaSessao($conexao, $usuario);
session_write_close();

$mensagem = trim($_POST['mensagem'] ?? '');
if ($mensagem === '') {
    respostaJson('nok', 'Digite uma mensagem antes de enviar.');
}

$tamanhoMensagem = function_exists('mb_strlen') ? mb_strlen($mensagem, 'UTF-8') : strlen($mensagem);
if ($tamanhoMensagem > 1000) {
    respostaJson('nok', 'A mensagem deve ter no máximo 1000 caracteres.');
}

$chatId = carregarChatPermitido($conexao, $usuario, $lojaId);
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

$stmt = $conexao->prepare("UPDATE chat_cotacao_usado SET atualizado_em = NOW() WHERE id = ?");
$stmt->bind_param('i', $chatId);
$stmt->execute();
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
