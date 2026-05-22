<?php
include_once '../conexao.php';
include_once '../funcoes.php';
include_once '_imagem.php';


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

$stmt = $conexao->prepare("SELECT id FROM produto WHERE id = ? AND loja_id = ? LIMIT 1");
$stmt->bind_param('ii', $id, $lojaId);
$stmt->execute();
$produtoExiste = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$produtoExiste) {
    $conexao->close();
    respostaJson('nok', 'Produto não encontrado ou sem permissão.');
}

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

if ($sucesso) {
    $imagem = registrarImagemProduto($conexao, $id);
    $conexao->close();
    respostaJson('ok', 'Produto atualizado com sucesso.', ['imagem' => $imagem]);
} else {
    $conexao->close();
    respostaJson('nok', 'Produto não encontrado ou sem permissão.');
}
