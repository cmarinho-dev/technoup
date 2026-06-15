<?php
include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'consumidor') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao consumidor.');
}
$consumidorId = (int)$_SESSION['usuario']['id'];
session_write_close();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJson('nok', 'Método inválido.');
}

$lojaId = (int)($_POST['loja_id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['tipoitem'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$detalhes = trim($_POST['detalhes'] ?? '');

$categoriasPermitidas = [
    'Computador',
    'Hardware',
    'Periférico',
    'Monitor',
    'Ergonomia',
    'Armazenamento',
    'Memória',
    'Placa de vídeo'
];
$estadosPermitidos = [
    'Funcionando perfeitamente',
    'Funcionando com avarias',
    'Não funciona'
];

if ($lojaId <= 0) {
    respostaJson('nok', 'Selecione a loja de destino.');
}

if (!valorEntre($nome, 3, 100)) {
    respostaJson('nok', 'Informe um nome de item com 3 a 100 caracteres.');
}

if (!in_array($categoria, $categoriasPermitidas, true)) {
    respostaJson('nok', 'Selecione um tipo de item válido.');
}

if (!in_array($estado, $estadosPermitidos, true)) {
    respostaJson('nok', 'Selecione o estado do item.');
}

$tamanhoDetalhes = function_exists('mb_strlen') ? mb_strlen($detalhes, 'UTF-8') : strlen($detalhes);
if ($tamanhoDetalhes > 1000) {
    respostaJson('nok', 'Os detalhes devem ter no máximo 1000 caracteres.');
}

$stmt = $conexao->prepare("
    SELECT loja.id
    FROM loja
    JOIN conta ON conta.id = loja.conta_id
    WHERE loja.id = ? AND conta.ativo = 1
    LIMIT 1
");
$stmt->bind_param('i', $lojaId);
$stmt->execute();
$loja = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$loja) {
    respostaJson('nok', 'Loja de destino não encontrada.');
}

$midias = salvarMidiasAvaliacao();

$conexao->begin_transaction();

$stmt = $conexao->prepare("
    INSERT INTO avaliacao_item
        (consumidor_id, loja_id, nome_item, categoria, estado, detalhes)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'iissss',
    $consumidorId,
    $lojaId,
    $nome,
    $categoria,
    $estado,
    $detalhes
);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    $stmt->close();
    $conexao->rollback();
    $conexao->close();
    respostaJson('nok', 'Não foi possível enviar a solicitação.');
}

$avaliacaoId = $stmt->insert_id;
$stmt->close();

if (count($midias) > 0) {
    $stmt = $conexao->prepare("
        INSERT INTO avaliacao_item_midia (avaliacao_id, arquivo, caminho, tipo_arquivo)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($midias as $midia) {
        $arquivo = $midia['arquivo'];
        $caminho = $midia['caminho'];
        $tipoArquivo = $midia['tipo_arquivo'];
        $stmt->bind_param('isss', $avaliacaoId, $arquivo, $caminho, $tipoArquivo);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            $stmt->close();
            $conexao->rollback();
            $conexao->close();
            respostaJson('nok', 'Não foi possível salvar os arquivos enviados.');
        }
    }

    $stmt->close();
}

$conexao->commit();
$conexao->close();

respostaJson('ok', 'Solicitação enviada para a loja.', ['id' => $avaliacaoId]);
