<?php
    include_once('_conexao.php');
    $retorno = [
        'status'    => '',
        'mensagem'  => '',
        'data'      => []
    ];
    // Simulando as informações que vem do front
    $email      = $_POST['email'];
    $senha      = $_POST['senha'];
    $cnpj       = $_POST['cnpj'];
    $cep_loja   = $_POST['cep_loja'];
    $nome_loja  = $_POST['nome_loja'];
    $telefone   = $_POST['telefone'];
    $ativo      = (int) $_POST['ativo'];

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("
    INSERT INTO lojista(email, senha, cnpj, cep_loja, nome_loja, telefone, ativo) VALUES(?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssi",$email, $senha, $cnpj, $cep_loja, $nome_loja, $telefone, $ativo);
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