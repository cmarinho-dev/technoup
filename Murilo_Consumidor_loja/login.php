<?php
include("conexao.php");

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM consumidor 
        WHERE email='$email' AND senha='$senha' AND ativo=1";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    echo "Login OK";
} else {
    echo "Erro no login";
}
?>