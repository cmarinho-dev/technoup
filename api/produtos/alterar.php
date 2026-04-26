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

$id    = (int)($_POST['id'] ?? 0);
$nome  = trim($_POST['nome'] ?? '');
$preco = (float)($_POST['preco'] ?? 0);

if (empty($id) || empty($nome) || $preco <= 0) {
    respostaJson('nok', 'ID, nome e preço são obrigatórios.');
}

$desconto  = (int)($_POST['desconto'] ?? 0);
$tipo      = $_POST['tipo'] ?? '';
$modelo    = $_POST['modelo'] ?? '';
$marca     = $_POST['marca'] ?? '';
$descricao = $_POST['descricao'] ?? '';

// preco_final é uma coluna GERADA — não incluir no UPDATE
$stmt = $conexao->prepare("
    UPDATE produto
    SET nome=?, preco=?, tipo=?, modelo=?, marca=?, descricao=?, desconto=?
    WHERE id=? AND loja_id=?
");
$stmt->bind_param('sdssssiii', $nome, $preco, $tipo, $modelo, $marca, $descricao, $desconto, $id, $lojaId);
$stmt->execute();

$sucesso = $stmt->affected_rows >= 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    respostaJson('ok', 'Produto atualizado com sucesso.');
} else {
    respostaJson('nok', 'Produto não encontrado ou sem permissão.');
}
