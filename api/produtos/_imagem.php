<?php

function registrarImagemProduto($conexao, $produtoId)
{
    $imagem = salvarImagem('produtos');
    if (!$imagem) {
        return null;
    }

    $descricao = 'imagem do produto';
    $stmt = $conexao->prepare("
        INSERT INTO imagem_produto (produto_id, arquivo, caminho, descricao)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            arquivo = VALUES(arquivo),
            caminho = VALUES(caminho),
            descricao = VALUES(descricao)
    ");
    $stmt->bind_param('isss', $produtoId, $imagem['arquivo'], $imagem['caminho'], $descricao);
    $stmt->execute();
    $stmt->close();

    return $imagem;
}
