<?php
    include_once('_conexao.php');
    $retorno = [
        'status'    => '',
        'mensagem'  => '',
        'data'      => []
    ];
    // Simulando as informações que vem do front
    $nome_loja    = $_POST['nome_loja'];
    $logradouro   = $_POST['logradouro'];
    $nome_lojista = $_POST['nome_lojista'];
    $cpf          = $_POST['cpf'];
    $cnpj         = $_POST['cnpj'];
    $cep_lojista  = $_POST['cep_lojista'];
    $estado       = $_POST['estado'];
    $cidade       = $_POST['cidade'];
    $bairro       = $_POST['bairro'];
    $numero       = $_POST['numero'];
    $genero       = $_POST['genero'];
    $email        = $_POST['email'];
    $senha        = $_POST['senha'];
    $telefone     = $_POST['telefone'];
    $ativo        = (int) $_POST['ativo'];

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("
    INSERT INTO lojista(nome_loja, logradouro, nome_lojista, cpf, cnpj, cep_lojista, estado, cidade, bairro, numero, genero, email, senha, telefone, ativo) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssssssi", $nome_loja, $logradouro, $nome_lojista, $cpf, $cnpj, $cep_lojista, $estado, $cidade, $bairro, $numero, $genero, $email, $senha, $telefone, $ativo);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $retorno = [
            'status' => 'ok',
            'mensagem' => 'registro inserido com sucesso',
            'data' => []
        ];
    }else{
        $retorno = [
            'status' => 'nok',
            'mensagem' => 'falha ao inserir o registro',
            'data' => []
        ];
    }

    $stmt->close();
    $conexao->close();

    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);