<?php
include_once '../conexao.php';

// Padrão de retorno
$retorno = [
    'status'   => '',
    'mensagem' => '',
    'data'     => []
];

$selectLojas = "
    SELECT
        loja.*,
        COALESCE(notas.media_atendimento, 0) AS media_atendimento,
        COALESCE(notas.total_avaliacoes_atendimento, 0) AS total_avaliacoes_atendimento
    FROM loja
    LEFT JOIN (
        SELECT
            loja_id,
            ROUND(AVG(nota), 1) AS media_atendimento,
            COUNT(id) AS total_avaliacoes_atendimento
        FROM avaliacao_atendimento
        GROUP BY loja_id
    ) notas ON notas.loja_id = loja.id
";

// Seleciona por conta_id, por id (se fornecido) ou todas as lojas
if (isset($_GET['conta_id']) && $_GET['conta_id'] !== '') {
    $contaId = intval($_GET['conta_id']);
    $stmt = $conexao->prepare($selectLojas . " WHERE loja.conta_id = ?");
    $stmt->bind_param('i', $contaId);
} elseif (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = intval($_GET['id']);
    $stmt = $conexao->prepare($selectLojas . " WHERE loja.id = ?");
    $stmt->bind_param('i', $id);
} else {
    $stmt = $conexao->prepare($selectLojas . "
    JOIN conta ON loja.conta_id = conta.id
    WHERE conta.tipo = 'lojista'
    AND conta.ativo = 1
    ORDER BY nome_loja");
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
