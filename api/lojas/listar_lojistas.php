<?php
include_once '../conexao.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao administrador.');
}
session_write_close();

$sql = "
    SELECT
        conta.id        AS conta_id,
        conta.nome      AS nome_conta,
        conta.email,
        conta.ativo,
        conta.criado_em,
        loja.nome_loja,
        loja.telefone,
        loja.cnpj,
        loja.cidade,
        loja.estado,
        loja.logradouro,
        loja.numero
    FROM conta
    LEFT JOIN loja ON loja.conta_id = conta.id
    WHERE conta.tipo = 'lojista'
    ORDER BY conta.criado_em DESC
";

$resultado = $conexao->query($sql);
$lojistas = [];
while ($linha = $resultado->fetch_assoc()) {
    $lojistas[] = $linha;
}

$conexao->close();
respostaJson('ok', '', $lojistas);
