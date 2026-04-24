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

if (empty($id)) {
    respostaJson('nok', 'ID do produto é obrigatório.');
}

// Deleta somente se o produto pertence à loja do lojista logado
$stmt = $conexao->prepare("DELETE FROM produto WHERE id = ? AND loja_id = ?");
$stmt->bind_param('ii', $id, $lojaId);
$stmt->execute();
$sucesso = $stmt->affected_rows > 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    respostaJson('ok', 'Produto deletado com sucesso.');
} else {
    respostaJson('nok', 'Produto não encontrado ou sem permissão para deletar.');
}
