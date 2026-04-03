<?php
include_once('_conexao.php');

$retorno = [
    'status' => '',
    'mensagem' => '',
    'data' => []
];

if (isset($_GET['id']) && isset($_POST['email'], $_POST['senha'], $_POST['cnpj'], $_POST['cep_loja'], $_POST['nome_loja'], $_POST['telefone'], $_POST['ativo'])) {
    $id = (int) $_GET['id'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cnpj = $_POST['cnpj'];
    $cep_loja = $_POST['cep_loja'];
    $nome_loja = $_POST['nome_loja'];
    $telefone = $_POST['telefone'];
    $ativo = (int) $_POST['ativo'];

    $stmt = $conexao->prepare("UPDATE lojista SET email = ?, senha = ?, cnpj = ?, cep_loja = ?, nome_loja = ?, telefone = ?, ativo = ? WHERE id = ?");
    $stmt->bind_param("ssssssii", $email, $senha, $cnpj, $cep_loja, $nome_loja, $telefone, $ativo, $id);

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'ok',
            'mensagem' => 'Registro alterado com sucesso.',
            'data' => []
        ];
    } else {
        $retorno = [
            'status' => 'nok',
            'mensagem' => 'Erro ao atualizar o lojista.',
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