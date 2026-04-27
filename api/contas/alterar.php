<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Você precisa estar logado.');
}
$idUsuario = (int)$_SESSION['usuario']['id'];
session_write_close();

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($nome) || empty($email)) {
    respostaJson('nok', 'Nome e email são obrigatórios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respostaJson('nok', 'Email inválido.');
}

// Atualiza com ou sem nova senha
if (!empty($senha)) {
    $stmt = $conexao->prepare("UPDATE conta SET nome = ?, email = ?, senha = ? WHERE id = ?");
    $stmt->bind_param('sssi', $nome, $email, $senha, $idUsuario);
} else {
    $stmt = $conexao->prepare("UPDATE conta SET nome = ?, email = ? WHERE id = ?");
    $stmt->bind_param('ssi', $nome, $email, $idUsuario);
}

$stmt->execute();
$sucesso = $stmt->affected_rows >= 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    // Atualiza os dados do usuário na sessão
    session_start();
    $_SESSION['usuario']['nome']  = $nome;
    $_SESSION['usuario']['email'] = $email;
    session_write_close();
    respostaJson('ok', 'Dados atualizados com sucesso.');
} else {
    respostaJson('nok', 'Não foi possível atualizar os dados.');
}