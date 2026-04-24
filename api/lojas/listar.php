<?php
include_once '../conexao.php';

$resultado = $conexao->query("SELECT * FROM loja ORDER BY nome_loja");
$lojas = [];
while ($loja = $resultado->fetch_assoc()) {
    $lojas[] = $loja;
}

$conexao->close();
respostaJson('ok', '', $lojas);
