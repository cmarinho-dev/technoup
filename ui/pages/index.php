<?php
$title = "Clientes";
include 'components/head.php';
include 'components/navbar.php';

$message = "Lista carregada!";
$type = "success";
include 'components/alert.php';

$columns = ["ID", "Nome", "Email"];
$data = [
    ["id"=>1, "nome"=>"Carlos", "email"=>"carlos@email.com"],
    ["id"=>2, "nome"=>"Ana", "email"=>"ana@email.com"]
];

include 'components/table.php';

include 'components/footer.php';