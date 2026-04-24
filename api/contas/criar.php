<?php
include_once '../conexao.php';

$dados = receberJson();

$nome  = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$senha = trim($dados['senha'] ?? '');
$tipo  = trim($dados['tipo'] ?? 'consumidor');

if (empty($nome) || empty($email) || empty($senha)) {
    respostaJson('nok', 'Nome, email e senha são obrigatórios.');
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

// Insere a nova conta
$stmt = $conexao->prepare("INSERT INTO conta (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $nome, $email, $senha, $tipo);
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
