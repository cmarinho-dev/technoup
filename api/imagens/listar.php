<?php
include_once '../conexao.php';

$produtoId = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : null;

if (!$produtoId) {
    respostaJson('nok', 'O parâmetro produto_id é obrigatório.');
}

$stmt = $conexao->prepare("SELECT * FROM imagem_produto WHERE produto_id = ?");
$stmt->bind_param('i', $produtoId);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

$imagens = [];
while ($imagem = $resultado->fetch_assoc()) {
    $imagens[] = $imagem;
}

$conexao->close();
respostaJson('ok', '', $imagens);
