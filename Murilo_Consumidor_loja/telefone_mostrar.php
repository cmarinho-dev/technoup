<?php
include("conexao.php");

$result = $con->query("SELECT nome_loja, telefone FROM lojista");

while($row = $result->fetch_assoc()){
    echo "Loja: " . $row['nome_loja'] . "<br>";
    echo "Telefone: " . $row['telefone'] . "<br>";
    echo "WhatsApp: " . $row['telefone'] . "<br><br>";
}
?>