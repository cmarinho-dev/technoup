<?php
include_once '../conexao.php';
include_once '../funcoes.php';


$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$cpf   = apenasDigitos($_POST['cpf'] ?? '');
$tipo  = trim($_POST['tipo'] ?? 'consumidor');

if (empty($nome) || empty($email) || empty($senha) || empty($cpf)) {
    respostaJson('nok', 'Nome, CPF, email e senha são obrigatórios.');
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

if (!in_array($tipo, ['consumidor', 'lojista'], true)) {
    respostaJson('nok', 'Tipo de conta inválido.');
}

$tamanhoSenha = function_exists('mb_strlen') ? mb_strlen($senha, 'UTF-8') : strlen($senha);
if ($tamanhoSenha < 6) {
    respostaJson('nok', 'A senha deve ter pelo menos 6 caracteres.');
}

// Verifica se o email já está cadastrado
$stmtVerifica = $conexao->prepare("SELECT id FROM conta WHERE email = ?");
$stmtVerifica->bind_param('s', $email);
$stmtVerifica->execute();
$stmtVerifica->store_result();
if ($stmtVerifica->num_rows > 0) {
    $stmtVerifica->close();
    respostaJson('nok', 'Este email já está cadastrado.');
}
$stmtVerifica->close();

$stmtVerifica = $conexao->prepare("SELECT id FROM conta WHERE cpf = ?");
$stmtVerifica->bind_param('s', $cpf);
$stmtVerifica->execute();
$stmtVerifica->store_result();
if ($stmtVerifica->num_rows > 0) {
    $stmtVerifica->close();
    respostaJson('nok', 'Este CPF já está cadastrado.');
}
$stmtVerifica->close();

// Insere a nova conta
$stmt = $conexao->prepare("INSERT INTO conta (nome, cpf, email, senha, tipo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $nome, $cpf, $email, $senha, $tipo);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $idCriado = $stmt->insert_id;
    $stmt->close();
    $conexao->close();
    respostaJson('ok', 'Conta criada com sucesso.', ['id' => $idCriado]);
} else {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível criar a conta.');
}
