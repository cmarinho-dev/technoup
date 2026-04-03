<?php
include_once('_conexao.php');

$retorno = ['status' => '', 'mensagem' => ''];

if(isset($_GET['id'])){
    $stmt = $conexao->prepare("DELETE FROM produto WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);

    if($stmt->execute()){
        $retorno = ['status' => 'ok', 'mensagem' => 'Excluído com sucesso.'];
    } else {
        $retorno = ['status' => 'nok', 'mensagem' => 'Erro ao excluir.'];
    }
    $stmt->close();
} else {
    $retorno = ['status' => 'nok', 'mensagem' => 'ID não informado.'];
}

$conexao->close();
header("Content-type:application/json;charset:utf-8");
echo json_encode($retorno);