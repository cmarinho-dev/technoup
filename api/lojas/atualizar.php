<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'lojista') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao lojista.');
}
$contaId = (int)$_SESSION['usuario']['id'];
session_write_close();

$nomeLoja = trim($_POST['nome_loja'] ?? '');
$cnpj     = trim($_POST['cnpj'] ?? '');

if (empty($nomeLoja) || empty($cnpj)) {
    respostaJson('nok', 'Nome da loja e CNPJ são obrigatórios.');
}

$telefone   = $_POST['telefone'] ?? '';
$cpf        = $_POST['cpf'] ?? '';
$cep        = $_POST['cep'] ?? '';
$estado     = $_POST['estado'] ?? '';
$cidade     = $_POST['cidade'] ?? '';
$bairro     = $_POST['bairro'] ?? '';
$logradouro = $_POST['logradouro'] ?? '';
$numero     = $_POST['numero'] ?? '';

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
