<?php
// Lista denuncias para administradores.

include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao administrador.');
}
session_write_close();

$status = trim($_GET['status'] ?? '');
$statusPermitidos = ['pendente', 'em_analise', 'resolvida', 'recusada'];

$sql = "
    SELECT
        denuncia_conta.id,
        denuncia_conta.motivo,
        denuncia_conta.status,
        denuncia_conta.criado_em,
        denuncia_conta.atualizado_em,
        denunciante.id AS denunciante_id,
        denunciante.nome AS denunciante_nome,
        denunciante.email AS denunciante_email,
        denunciante.tipo AS denunciante_tipo,
        denunciado.id AS denunciado_id,
        denunciado.nome AS denunciado_nome,
        denunciado.email AS denunciado_email,
        denunciado.tipo AS denunciado_tipo,
        loja.nome_loja AS denunciado_nome_loja
    FROM denuncia_conta
    JOIN conta denunciante ON denunciante.id = denuncia_conta.denunciante_id
    JOIN conta denunciado ON denunciado.id = denuncia_conta.denunciado_id
    LEFT JOIN loja ON loja.conta_id = denunciado.id
";

if ($status !== '' && in_array($status, $statusPermitidos, true)) {
    $sql .= " WHERE denuncia_conta.status = ?";
    $stmt = $conexao->prepare($sql . " ORDER BY denuncia_conta.criado_em DESC");
    $stmt->bind_param('s', $status);
} else {
    $stmt = $conexao->prepare($sql . " ORDER BY denuncia_conta.criado_em DESC");
}

$stmt->execute();
$resultado = $stmt->get_result();

$denuncias = [];
while ($linha = $resultado->fetch_assoc()) {
    $denuncias[] = $linha;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $denuncias);
