<?php
include_once '../conexao.php';
include_once '../funcoes.php';


session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Você precisa estar logado.');
}
$idUsuario = (int)$_SESSION['usuario']['id'];
session_write_close();

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf   = apenasDigitos($_POST['cpf'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($nome) || empty($email) || empty($cpf)) {
    respostaJson('nok', 'Nome, CPF e email são obrigatórios.');
}

if (!valorEntre($nome, 3, 200)) {
    respostaJson('nok', 'Informe o nome completo com pelo menos 3 caracteres.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respostaJson('nok', 'Email inválido.');
}

if (!cpfValido($cpf)) {
    respostaJson('nok', 'CPF inválido.');
}

$stmtDuplicado = $conexao->prepare("SELECT id FROM conta WHERE (email = ? OR cpf = ?) AND id <> ?");
$stmtDuplicado->bind_param('ssi', $email, $cpf, $idUsuario);
$stmtDuplicado->execute();
$duplicado = $stmtDuplicado->get_result()->fetch_assoc();
$stmtDuplicado->close();

if ($duplicado) {
    respostaJson('nok', 'Email ou CPF já cadastrado em outra conta.');
}

// Atualiza com ou sem nova senha
if (!empty($senha)) {
    $stmt = $conexao->prepare("UPDATE conta SET nome = ?, cpf = ?, email = ?, senha = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $nome, $cpf, $email, $senha, $idUsuario);
} else {
    $stmt = $conexao->prepare("UPDATE conta SET nome = ?, cpf = ?, email = ? WHERE id = ?");
    $stmt->bind_param('sssi', $nome, $cpf, $email, $idUsuario);
}

$stmt->execute();
$sucesso = $stmt->affected_rows >= 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    // Atualiza os dados do usuário na sessão
    session_start();
    $_SESSION['usuario']['nome']  = $nome;
    $_SESSION['usuario']['cpf']   = $cpf;
    $_SESSION['usuario']['email'] = $email;
    session_write_close();
    respostaJson('ok', 'Dados atualizados com sucesso.');
} else {
    respostaJson('nok', 'Não foi possível atualizar os dados.');
}
