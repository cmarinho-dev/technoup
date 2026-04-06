<?php
$title = "Clientes";
include '../components/head.php';

// adicionando navbar
include '../components/navbar.php';

// usando alert
$message = "Lista carregada!";
$type = "success"; // tipos: success, danger, warning, info
include '../components/alert.php';

// criando tabela
$columns = ["ID", "Nome", "Email"];
$data = [
    ["id"=>1, "nome"=>"Carlos", "email"=>"carlos@email.com"],
    ["id"=>2, "nome"=>"Ana", "email"=>"ana@email.com"]
];
include '../components/table.php';

// usando form
$action = "../../php/arquivo_php.php";
$fields = [
    "usuario" => "Usuário",
    "senha" => "Senha"
];
include '../components/form.php'

// adicionando footer
include '../components/footer.php';
