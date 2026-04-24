<?php
include_once '../conexao.php';

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
    UPDATE loja
    SET nome_loja=?, telefone=?, cpf=?, cnpj=?, cep=?, estado=?, cidade=?, bairro=?, logradouro=?, numero=?
    WHERE conta_id=?
");
$stmt->bind_param('ssssssssssi', $nomeLoja, $telefone, $cpf, $cnpj, $cep, $estado, $cidade, $bairro, $logradouro, $numero, $contaId);
$stmt->execute();

$sucesso = $stmt->affected_rows >= 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    respostaJson('ok', 'Loja atualizada com sucesso.');
} else {
    respostaJson('nok', 'Não foi possível atualizar a loja.');
}
