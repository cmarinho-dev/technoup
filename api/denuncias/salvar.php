<?php
// Registra uma denuncia contra uma conta de consumidor ou lojista.

include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Faça login para enviar uma denúncia.');
}

$denuncianteId = (int) $_SESSION['usuario']['id'];
$denuncianteTipo = $_SESSION['usuario']['tipo'];
session_write_close();

$denunciadoId = (int) ($_POST['denunciado_id'] ?? 0);
$motivo = trim($_POST['motivo'] ?? '');

if ($denunciadoId <= 0) {
    respostaJson('nok', 'Selecione a conta denunciada.');
}

if ($denunciadoId === $denuncianteId) {
    respostaJson('nok', 'Você não pode denunciar a própria conta.');
}

if ($motivo === '' || strlen($motivo) < 10) {
    respostaJson('nok', 'Informe o motivo da denúncia com pelo menos 10 caracteres.');
}

$tipoPermitido = '';
if ($denuncianteTipo === 'consumidor') {
    $tipoPermitido = 'lojista';
} elseif ($denuncianteTipo === 'lojista') {
    $tipoPermitido = 'consumidor';
} else {
    respostaJson('nok', 'Apenas clientes e lojistas podem enviar denúncias.');
}

$stmtConta = $conexao->prepare("SELECT id FROM conta WHERE id = ? AND tipo = ? AND ativo = 1");
$stmtConta->bind_param('is', $denunciadoId, $tipoPermitido);
$stmtConta->execute();
$resultadoConta = $stmtConta->get_result();
$stmtConta->close();

if ($resultadoConta->num_rows === 0) {
    respostaJson('nok', 'Conta denunciada não encontrada.');
}

$stmt = $conexao->prepare("
    INSERT INTO denuncia_conta (denunciante_id, denunciado_id, motivo)
    VALUES (?, ?, ?)
");
$stmt->bind_param('iis', $denuncianteId, $denunciadoId, $motivo);
$stmt->execute();
$sucesso = $stmt->affected_rows > 0;
$denunciaId = $stmt->insert_id;
$stmt->close();
$conexao->close();

if (!$sucesso) {
    respostaJson('nok', 'Não foi possível registrar a denúncia.');
}

respostaJson('ok', 'Denúncia registrada com sucesso.', ['id' => $denunciaId]);
