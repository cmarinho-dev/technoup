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

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
$lojaId = lojaDaSessao($conexao, $usuario);
session_write_close();

$where = '';
$params = [];
$types = '';

if ($usuario['tipo'] === 'consumidor') {
    $where = 'chat_cotacao_item.consumidor_id = ?';
    $params[] = (int)$usuario['id'];
    $types .= 'i';
} elseif ($usuario['tipo'] === 'lojista') {
    if (!$lojaId) respostaJson('nok', 'Loja não encontrada na sessão.');
    $where = 'chat_cotacao_item.loja_id = ?';
    $params[] = $lojaId;
    $types .= 'i';
} elseif ($usuario['tipo'] === 'administrador') {
    $where = '1 = 1';
} else {
    respostaJson('nok', 'Acesso restrito.');
}

$sql = "
    SELECT
        chat_cotacao_item.id AS chat_id,
        chat_cotacao_item.status AS chat_status,
        chat_cotacao_item.avaliacao_id,
        chat_cotacao_item.atualizado_em,
        chat_cotacao_item.lido_consumidor_em,
        chat_cotacao_item.lido_lojista_em,
        avaliacao_item.nome_item,
        avaliacao_item.categoria,
        avaliacao_item.estado,
        loja.nome_loja,
        loja.banner_img,
        conta.nome AS consumidor_nome,
        ultima.mensagem AS ultima_mensagem,
        ultima.criado_em AS ultima_mensagem_em
    FROM chat_cotacao_item
    JOIN avaliacao_item ON avaliacao_item.id = chat_cotacao_item.avaliacao_id
    JOIN loja ON loja.id = chat_cotacao_item.loja_id
    JOIN conta ON conta.id = chat_cotacao_item.consumidor_id
    LEFT JOIN mensagem_cotacao_item ultima ON ultima.id = (
        SELECT mensagem_cotacao_item.id
        FROM mensagem_cotacao_item
        WHERE mensagem_cotacao_item.chat_id = chat_cotacao_item.id
        ORDER BY mensagem_cotacao_item.id DESC
        LIMIT 1
    )
    WHERE {$where}
    ORDER BY chat_cotacao_item.atualizado_em DESC, chat_cotacao_item.id DESC
";

$stmt = $conexao->prepare($sql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();

$chats = [];
while ($linha = $resultado->fetch_assoc()) {
    $chats[] = $linha;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $chats);
