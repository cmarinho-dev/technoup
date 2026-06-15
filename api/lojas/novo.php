<?php
include_once '../conexao.php';
include_once '../funcoes.php';


// Obtém o conta_id da sessão do lojista logado
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

$stmtLojaExistente = $conexao->prepare("SELECT id FROM loja WHERE conta_id = ? LIMIT 1");
$stmtLojaExistente->bind_param('i', $contaId);
$stmtLojaExistente->execute();
$lojaJaExiste = $stmtLojaExistente->get_result()->num_rows > 0;
$stmtLojaExistente->close();

if ($lojaJaExiste) {
    respostaJson('nok', 'Esta conta já possui uma loja cadastrada.');
}

$stmtDuplicado = $conexao->prepare("SELECT id FROM loja WHERE cnpj = ? LIMIT 1");
$stmtDuplicado->bind_param('s', $cnpj);
$stmtDuplicado->execute();
$cnpjExiste = $stmtDuplicado->get_result()->num_rows > 0;
$stmtDuplicado->close();

if ($cnpjExiste) {
    respostaJson('nok', 'Este CNPJ já está cadastrado.');
}

$stmt = $conexao->prepare("
    INSERT INTO loja (conta_id, nome_loja, telefone, cnpj, cep, estado, cidade, bairro, logradouro, numero)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('isssssssss', $contaId, $nomeLoja, $telefone, $cnpj, $cep, $estado, $cidade, $bairro, $logradouro, $numero);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $idCriado = $stmt->insert_id;
    $stmt->close();

    // Busca os dados completos da loja criada e salva na sessão
    $stmtLoja = $conexao->prepare("SELECT * FROM loja WHERE id = ?");
    $stmtLoja->bind_param('i', $idCriado);
    $stmtLoja->execute();
    $lojaCriada = $stmtLoja->get_result()->fetch_assoc();
    $stmtLoja->close();
    $conexao->close();

    session_start();
    $_SESSION['loja']['id'] = $lojaCriada['id'];
    session_write_close();

    respostaJson('ok', 'Loja criada com sucesso.', ['id' => $idCriado]);
} else {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível criar a loja.');
}
