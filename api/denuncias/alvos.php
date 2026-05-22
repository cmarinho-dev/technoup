<?php
// Lista contas que podem ser denunciadas pelo usuario logado.

include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Faça login para denunciar uma conta.');
}

$usuarioId = (int) $_SESSION['usuario']['id'];
$usuarioTipo = $_SESSION['usuario']['tipo'];
session_write_close();

$tipoAlvo = '';
if ($usuarioTipo === 'consumidor') {
    $tipoAlvo = 'lojista';
} elseif ($usuarioTipo === 'lojista') {
    $tipoAlvo = 'consumidor';
} else {
    respostaJson('nok', 'Apenas clientes e lojistas podem enviar denúncias.');
}

$sql = "
    SELECT
        conta.id,
        conta.nome,
        conta.email,
        conta.tipo,
        loja.nome_loja
    FROM conta
    LEFT JOIN loja ON loja.conta_id = conta.id
    WHERE conta.id <> ?
      AND conta.tipo = ?
      AND conta.ativo = 1
    ORDER BY conta.tipo DESC, COALESCE(loja.nome_loja, conta.nome)
";

$stmt = $conexao->prepare($sql);
$stmt->bind_param('is', $usuarioId, $tipoAlvo);
$stmt->execute();
$resultado = $stmt->get_result();

$contas = [];
while ($linha = $resultado->fetch_assoc()) {
    $contas[] = $linha;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $contas);
