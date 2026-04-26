<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

// Obtém o conta_id da sessão do lojista logado
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
    $_SESSION['loja']['id'] = $lojaCriada['id'];
    session_write_close();

    respostaJson('ok', 'Loja criada com sucesso.', ['id' => $idCriado]);
} else {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível criar a loja.');
}
