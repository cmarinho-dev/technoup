<?php
include_once '../conexao.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Você precisa estar logado.');
}
$idUsuario = (int)$_SESSION['usuario']['id'];
session_write_close();

$dados = receberJson();

$nome  = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$senha = trim($dados['senha'] ?? '');

if (empty($nome) || empty($email)) {
    respostaJson('nok', 'Nome e email são obrigatórios.');
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
