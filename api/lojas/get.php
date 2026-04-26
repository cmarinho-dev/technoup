<?php
include_once '../conexao.php';

// Padrão de retorno
$retorno = [
    'status'   => '',
    'mensagem' => '',
    'data'     => []
];

// Seleciona por conta_id, por id (se fornecido) ou todas as lojas
if (isset($_GET['conta_id']) && $_GET['conta_id'] !== '') {
    $contaId = intval($_GET['conta_id']);
    $stmt = $conexao->prepare("SELECT * FROM loja WHERE conta_id = ?");
    $stmt->bind_param('i', $contaId);
} elseif (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = intval($_GET['id']);
    $stmt = $conexao->prepare("SELECT * FROM loja WHERE id = ?");
    $stmt->bind_param('i', $id);
} else {
    $stmt = $conexao->prepare("SELECT * FROM loja ORDER BY nome_loja");
}

$stmt->execute();
$resultado = $stmt->get_result();

$tabela = [];
if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $tabela[] = $linha;
    }

    $retorno = [
        'status'   => 'ok',
        'mensagem' => 'Sucesso, consulta efetuada.',
        'data'     => $tabela
    ];
} else {
    $retorno = [
        'status'   => 'nok',
        'mensagem' => 'Não há registros',
        'data'     => []
    ];
}

$stmt->close();
$conexao->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($retorno);