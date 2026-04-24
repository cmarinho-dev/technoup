<?php
include_once '../conexao.php';

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

$dados = receberJson();

$nome      = trim($dados['nome'] ?? '');
$preco     = (float)($dados['preco'] ?? 0);
$desconto  = (int)($dados['desconto'] ?? 0);

if (empty($nome) || $preco <= 0) {
    respostaJson('nok', 'Nome e preço são obrigatórios.');
}

$tipo      = $dados['tipo'] ?? '';
$modelo    = $dados['modelo'] ?? '';
$marca     = $dados['marca'] ?? '';
$descricao = $dados['descricao'] ?? '';

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
