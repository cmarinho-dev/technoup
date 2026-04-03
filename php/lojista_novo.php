<?php
include_once('_conexao.php');

header("Content-type: application/json; charset=utf-8");

$retorno = [
    'status' => '',
    'mensagem' => '',
    'data' => []
];

// Verificar se todos os campos necessários foram enviados
$campos_necessarios = ['email', 'senha', 'cnpj', 'cep_loja', 'nome_loja', 'telefone', 'ativo'];
foreach ($campos_necessarios as $campo) {
    if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
        $retorno = [
            'status' => 'nok',
            'mensagem' => "Campo '$campo' é obrigatório.",
            'data' => []
        ];
        echo json_encode($retorno);
        exit;
    }
}

// Simulando as informações que vem do front
$email = trim($_POST['email']);
$senha = trim($_POST['senha']);
$cnpj = trim($_POST['cnpj']);
$cep_loja = trim($_POST['cep_loja']);
$nome_loja = trim($_POST['nome_loja']);
$telefone = trim($_POST['telefone']);
$ativo = (int) $_POST['ativo'];

// Preparando para inserção no banco de dados
$stmt = $conexao->prepare("INSERT INTO lojista(email, senha, cnpj, cep_loja, nome_loja, telefone, ativo) VALUES(?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssi", $email, $senha, $cnpj, $cep_loja, $nome_loja, $telefone, $ativo);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $retorno = [
            'status' => 'ok',
            'mensagem' => 'Registro inserido com sucesso.',
            'data' => []
        ];
    } else {
        $retorno = [
            'status' => 'nok',
            'mensagem' => 'Falha ao inserir o registro.',
            'data' => []
        ];
    }
} else {
    $retorno = [
        'status' => 'nok',
        'mensagem' => 'Erro na execução da query: ' . $stmt->error,
        'data' => []
    ];
}

$stmt->close();
$conexao->close();

echo json_encode($retorno);
exit;