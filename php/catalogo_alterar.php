<?php
include_once('_conexao.php');

$retorno = [
    'status' => '',
    'mensagem' => '',
    'data' => []
];

if (isset($_POST['id'], $_POST['nome'], $_POST['preco'], $_POST['tipo'])) {
    $id = (int) $_POST['id'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $tipo = $_POST['tipo'];

    $stmt = $conexao->prepare("UPDATE produto SET nome = ?, preco = ?, tipo = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $nome, $preco, $tipo, $id);

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'ok',
            'mensagem' => 'Produto atualizado com sucesso.',
            'data' => []
        ];
    } else {
        $retorno = [
            'status' => 'nok',
            'mensagem' => 'Erro ao atualizar o produto.',
            'data' => []
        ];
    }
    $stmt->close();
} else {
    $retorno = [
        'status' => 'nok',
        'mensagem' => 'Dados incompletos para atualização.',
        'data' => []
    ];
}

$conexao->close();

header("Content-type: application/json; charset=utf-8");
echo json_encode($retorno);
exit;
