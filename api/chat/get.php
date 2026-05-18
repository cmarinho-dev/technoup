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
    $avaliacaoId = isset($_GET['avaliacao_id']) ? (int)$_GET['avaliacao_id'] : 0;
    $chatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;

    $params = [];
    $types = '';
    $where = '';

    if ($chatId > 0) {
        $where = 'chat_cotacao_item.id = ?';
        $params[] = $chatId;
        $types .= 'i';
    } elseif ($avaliacaoId > 0) {
        $where = 'avaliacao_item.id = ?';
        $params[] = $avaliacaoId;
        $types .= 'i';
    } else {
        $where = 'avaliacao_item.status = \'aceita\'';
    }

    if ($usuario['tipo'] === 'consumidor') {
        $where .= ' AND avaliacao_item.consumidor_id = ?';
        $params[] = (int)$usuario['id'];
        $types .= 'i';
    } elseif ($usuario['tipo'] === 'lojista') {
        if (!$lojaId) respostaJson('nok', 'Loja não encontrada na sessão.');
        $where .= ' AND avaliacao_item.loja_id = ?';
        $params[] = $lojaId;
        $types .= 'i';
    } elseif ($usuario['tipo'] !== 'administrador') {
        respostaJson('nok', 'Acesso restrito.');
    }

    $sql = "
        SELECT
            chat_cotacao_item.id,
            chat_cotacao_item.consumidor_id,
            chat_cotacao_item.loja_id,
            chat_cotacao_item.avaliacao_id,
            chat_cotacao_item.status AS chat_status,
            chat_cotacao_item.criado_em,
            loja.nome_loja,
            loja.banner_img,
            avaliacao_item.nome_item,
            avaliacao_item.categoria,
            avaliacao_item.estado,
            avaliacao_item.detalhes,
            avaliacao_item.status
        FROM chat_cotacao_item
        JOIN avaliacao_item ON avaliacao_item.id = chat_cotacao_item.avaliacao_id
        JOIN loja ON loja.id = chat_cotacao_item.loja_id
        WHERE {$where}
        ORDER BY chat_cotacao_item.id DESC
        LIMIT 1
    ";

    $stmt = $conexao->prepare($sql);
    if ($types !== '') $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $chat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$chat) {
        respostaJson('nok', 'Nenhum chat liberado. A loja precisa aceitar uma solicitação de avaliação primeiro.');
    }

    if ($chat['status'] !== 'aceita') {
        respostaJson('nok', 'Este chat ainda não foi liberado pela loja.');
    }

    return $chat;
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
$lojaId = lojaDaSessao($conexao, $usuario);
session_write_close();

$ultimoId = isset($_GET['ultimo_id']) ? (int)$_GET['ultimo_id'] : 0;
$chat = carregarChatPermitido($conexao, $usuario, $lojaId);

$stmt = $conexao->prepare("
    SELECT id, is_cliente, chat_id, mensagem, criado_em
    FROM mensagem_cotacao_item
    WHERE chat_id = ? AND id > ?
    ORDER BY id ASC
");
$stmt->bind_param('ii', $chat['id'], $ultimoId);
$stmt->execute();
$resultado = $stmt->get_result();

$mensagens = [];
while ($linha = $resultado->fetch_assoc()) {
    $mensagens[] = $linha;
}
$stmt->close();
$conexao->close();

respostaJson('ok', '', [
    'chat' => [
        'id' => (int)$chat['id'],
        'consumidor_id' => (int)$chat['consumidor_id'],
        'loja_id' => (int)$chat['loja_id'],
        'avaliacao_id' => (int)$chat['avaliacao_id'],
        'status' => $chat['chat_status'],
        'criado_em' => $chat['criado_em']
    ],
    'loja' => [
        'id' => (int)$chat['loja_id'],
        'nome_loja' => $chat['nome_loja'],
        'banner_img' => $chat['banner_img']
    ],
    'avaliacao' => [
        'id' => (int)$chat['avaliacao_id'],
        'nome_item' => $chat['nome_item'],
        'categoria' => $chat['categoria'],
        'estado' => $chat['estado'],
        'detalhes' => $chat['detalhes'],
        'status' => $chat['status']
    ],
    'usuario' => [
        'id' => (int)$usuario['id'],
        'nome' => $usuario['nome'],
        'tipo' => $usuario['tipo']
    ],
    'mensagens' => $mensagens
]);
