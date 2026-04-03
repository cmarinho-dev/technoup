<?php
include_once('_conexao.php');

$retorno = [
    'status' => '',
    'mensagem' => '',
    'data' => []
];

if (isset($_GET['id'])) {
    $stmt = $conexao->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
} else {
    $stmt = $conexao->prepare("SELECT * FROM produto");
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $retorno['data'][] = $linha;
    }
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Consulta realizada com sucesso.';
} else {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Nenhum registro encontrado.';
}

$stmt->close();
$conexao->close();

header("Content-type: application/json; charset=utf-8");
echo json_encode($retorno);
exit;
