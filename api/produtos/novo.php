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
$lojaId = (int)($_SESSION['loja']['id'] ?? 0);
session_write_close();

if (empty($lojaId)) {
    respostaJson('nok', 'Loja não encontrada na sessão. Cadastre sua loja primeiro.');
}

$nome      = trim($_POST['nome'] ?? '');
$preco     = (float)($_POST['preco'] ?? 0);
$desconto  = (int)($_POST['desconto'] ?? 0);

if (empty($nome) || $preco <= 0) {
    respostaJson('nok', 'Nome e preço são obrigatórios.');
}

$tipo      = $_POST['tipo'] ?? '';
$modelo    = $_POST['modelo'] ?? '';
$marca     = $_POST['marca'] ?? '';
$descricao = $_POST['descricao'] ?? '';

// preco_final é uma coluna GERADA pelo banco — não incluir no INSERT
$stmt = $conexao->prepare("
    INSERT INTO produto (loja_id, nome, preco, tipo, modelo, marca, descricao, desconto)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('isdssssi', $lojaId, $nome, $preco, $tipo, $modelo, $marca, $descricao, $desconto);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $idCriado = $stmt->insert_id;
    $stmt->close();
    $conexao->close();
    respostaJson('ok', 'Produto criado com sucesso.', ['id' => $idCriado]);
} else {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível criar o produto.');
}
