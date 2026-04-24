<?php
include_once '../conexao.php';

// Obtém o conta_id da sessão do lojista logado
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'lojista') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao lojista.');
}
$contaId = (int)$_SESSION['usuario']['id'];
session_write_close();

$dados = receberJson();

$nomeLoja = trim($dados['nome_loja'] ?? '');
$cnpj     = trim($dados['cnpj'] ?? '');

if (empty($nomeLoja) || empty($cnpj)) {
    respostaJson('nok', 'Nome da loja e CNPJ são obrigatórios.');
}

$telefone   = $dados['telefone'] ?? '';
$cpf        = $dados['cpf'] ?? '';
$cep        = $dados['cep'] ?? '';
$estado     = $dados['estado'] ?? '';
$cidade     = $dados['cidade'] ?? '';
$bairro     = $dados['bairro'] ?? '';
$logradouro = $dados['logradouro'] ?? '';
$numero     = $dados['numero'] ?? '';

$stmt = $conexao->prepare("
    INSERT INTO loja (conta_id, nome_loja, telefone, cpf, cnpj, cep, estado, cidade, bairro, logradouro, numero)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('issssssssss', $contaId, $nomeLoja, $telefone, $cpf, $cnpj, $cep, $estado, $cidade, $bairro, $logradouro, $numero);
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
    $_SESSION['loja'] = $lojaCriada;
    session_write_close();

    respostaJson('ok', 'Loja criada com sucesso.', ['id' => $idCriado]);
} else {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível criar a loja.');
}
