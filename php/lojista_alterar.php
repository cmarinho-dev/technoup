<?php
    include_once('_conexao.php');

    $retorno = [
        'status'    => '',
        'mensagem'  => '',
        'data'      => []
    ];

    if(isset($_GET['id'])){
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
        $id           = $_POST['id'];

        // Preparando para atualização no banco de dados

        $stmt = $conexao->prepare("UPDATE lojista SET nome_loja = ?, logradouro = ?, nome_lojista = ?, cpf = ?, cnpj = ?, cep_lojista = ?, estado = ?, cidade = ?, bairro = ?, numero = ?, genero = ?, email = ?, senha = ?, telefone = ?, ativo = ? WHERE id = ?");
        $stmt->bind_param("sssssssssssssssi", $nome_loja, $logradouro, $nome_lojista, $cpf, $cnpj, $cep_lojista, $estado, $cidade, $bairro, $numero, $genero, $email, $senha, $telefone, $ativo, $id);
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