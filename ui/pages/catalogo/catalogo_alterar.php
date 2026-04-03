<?php
include_once('_conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

if(isset($_GET['id']) && isset($_POST['nome'])){
    $id    = $_GET['id'];
    $nome  = $_POST['nome'];
    $preco = $_POST['preco'];
    $tipo  = $_POST['tipo'];

    $stmt = $conexao->prepare("UPDATE produto SET nome = ?, preco = ?, tipo = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $nome, $preco, $tipo, $id);

    if($stmt->execute()){
        $retorno = ['status' => 'ok', 'mensagem' => 'Produto alterado.'];
    } else {
        $retorno = ['status' => 'nok', 'mensagem' => 'Erro ao atualizar banco.'];
    }
    $stmt->close();
} else {
    $retorno = ['status' => 'nok', 'mensagem' => 'Dados incompletos.'];
}

$conexao->close();
header("Content-type:application/json;charset:utf-8");
echo json_encode($retorno);