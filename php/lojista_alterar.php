<?php
    include_once('_conexao.php');

    $retorno = [
        'status'    => '',
        'mensagem'  => '',
        'data'      => []
    ];

    if(isset($_GET['id'])){
        // Simulando as informações que vem do front
        $email      = $_POST['email'];
        $senha      = $_POST['senha'];
        $cnpj       = $_POST['cnpj'];
        $cep_loja   = $_POST['cep_loja'];
        $nome_loja  = $_POST['nome_loja'];
        $telefone   = $_POST['telefone'];
        $ativo      = (int) $_POST['ativo'];
    
        // Preparando para atualização no banco de dados
        $stmt = $conexao->prepare("UPDATE lojista SET email = ?, senha = ?, cnpj = ?, cep_loja = ?, nome_loja = ?, telefone = ?, ativo = ? WHERE id = ?");
        $stmt->bind_param("ssssssii",$email, $senha, $cnpj, $cep_loja, $nome_loja, $telefone, $ativo, $_GET['id']);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $retorno = [
                'status'    => 'ok',
                'mensagem'  => 'Registro alterado com sucesso.',
                'data'      => []
            ];
        }else{
            $retorno = [
                'status'    => 'nok',
                'mensagem'  => 'Não posso alterar um registro.'.json_encode($_GET),
                'data'      => []
            ];
        }
        $stmt->close();
    }else{
        $retorno = [
            'status'    => 'nok',
            'mensagem'  => 'Não posso alterar um registro sem um ID informado.',
            'data'      => []
        ];
    }
       
    $conexao->close();

    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);