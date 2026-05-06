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
        SELECT loja.id, loja.nome_loja, loja.banner_img, loja.conta_id
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
        SELECT id, consumidor_id, loja_id, criado_em
        FROM chat_cotacao_usado
        WHERE consumidor_id = ? AND loja_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('ii', $consumidorId, $lojaId);
    $stmt->execute();
    $chat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$chat) {
        $stmt = $conexao->prepare("
            INSERT INTO chat_cotacao_usado (consumidor_id, loja_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param('ii', $consumidorId, $lojaId);
        $stmt->execute();
        $chatId = $stmt->insert_id;
        $stmt->close();

        $chat = [
            'id' => $chatId,
            'consumidor_id' => $consumidorId,
            'loja_id' => $lojaId,
            'criado_em' => date('Y-m-d H:i:s')
        ];
    }

    return [$chat, $loja];
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
session_write_close();

$lojaId = 1;
$ultimoId = isset($_GET['ultimo_id']) ? (int)$_GET['ultimo_id'] : 0;

[$chat, $loja] = buscarOuCriarChat($conexao, $usuario, $lojaId);

$stmt = $conexao->prepare("
    SELECT id, is_cliente, chat_id, mensagem, criado_em
    FROM mensagem_cotacao_usado
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
    'chat' => $chat,
    'loja' => $loja,
    'usuario' => [
        'id' => (int)$usuario['id'],
        'nome' => $usuario['nome'],
        'tipo' => $usuario['tipo']
    ],
    'mensagens' => $mensagens
]);
