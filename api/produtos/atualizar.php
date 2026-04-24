<?php
include_once '../conexao.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'lojista') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao lojista.');
}
$lojaId = (int)($_SESSION['loja']['id'] ?? 0);
session_write_close();

$dados = receberJson();

$id    = (int)($dados['id'] ?? 0);
$nome  = trim($dados['nome'] ?? '');
$preco = (float)($dados['preco'] ?? 0);

if (empty($id) || empty($nome) || $preco <= 0) {
    respostaJson('nok', 'ID, nome e preço são obrigatórios.');
}

$desconto  = (int)($dados['desconto'] ?? 0);
$tipo      = $dados['tipo'] ?? '';
$modelo    = $dados['modelo'] ?? '';
$marca     = $dados['marca'] ?? '';
$descricao = $dados['descricao'] ?? '';

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
