<?php
include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'lojista') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao lojista.');
}
$contaId = (int)$_SESSION['usuario']['id'];
session_write_close();

$nomeLoja = trim($_POST['nome_loja'] ?? '');
$cnpj     = apenasDigitos($_POST['cnpj'] ?? '');

if (empty($nomeLoja) || empty($cnpj)) {
    respostaJson('nok', 'Nome da loja e CNPJ são obrigatórios.');
}

if (!valorEntre($nomeLoja, 3, 100)) {
    respostaJson('nok', 'Informe um nome de loja com 3 a 100 caracteres.');
}

if (!cnpjValido($cnpj)) {
    respostaJson('nok', 'CNPJ inválido.');
}

$telefone   = apenasDigitos($_POST['telefone'] ?? '');
$cep        = apenasDigitos($_POST['cep'] ?? '');
$estado     = strtoupper(trim($_POST['estado'] ?? ''));
$cidade     = trim($_POST['cidade'] ?? '');
$bairro     = trim($_POST['bairro'] ?? '');
$logradouro = trim($_POST['logradouro'] ?? '');
$numero     = trim($_POST['numero'] ?? '');

if ($telefone !== '' && !in_array(strlen($telefone), [10, 11], true)) {
    respostaJson('nok', 'Telefone deve ter DDD e 10 ou 11 dígitos.');
}

if ($cep !== '' && strlen($cep) !== 8) {
    respostaJson('nok', 'CEP deve ter 8 dígitos.');
}

if ($estado !== '' && !preg_match('/^[A-Z]{2}$/', $estado)) {
    respostaJson('nok', 'Estado deve ser informado pela sigla com 2 letras.');
}

if ($cidade !== '' && !valorEntre($cidade, 2, 100)) {
    respostaJson('nok', 'Cidade deve ter entre 2 e 100 caracteres.');
}

$stmtDuplicado = $conexao->prepare("SELECT id FROM loja WHERE cnpj = ? AND conta_id <> ? LIMIT 1");
$stmtDuplicado->bind_param('si', $cnpj, $contaId);
$stmtDuplicado->execute();
$cnpjExiste = $stmtDuplicado->get_result()->num_rows > 0;
$stmtDuplicado->close();

if ($cnpjExiste) {
    respostaJson('nok', 'Este CNPJ já está cadastrado.');
}

$stmt = $conexao->prepare("
    UPDATE loja
    SET nome_loja=?, telefone=?, cnpj=?, cep=?, estado=?, cidade=?, bairro=?, logradouro=?, numero=?
    WHERE conta_id=?
");
$stmt->bind_param('sssssssssi', $nomeLoja, $telefone, $cnpj, $cep, $estado, $cidade, $bairro, $logradouro, $numero, $contaId);
$stmt->execute();

$sucesso = $stmt->affected_rows >= 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    // Atualiza os dados do usuário na sessão
    session_start();
    //TODO implementar a sessao os dados da loja
    session_write_close();
    respostaJson('ok', 'Loja atualizada com sucesso.');
} 
else {
    respostaJson('nok', 'Não foi possível atualizar a loja.');
}
