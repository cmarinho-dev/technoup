<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "technoup");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consumidor_id = $_SESSION['usuario']['id'] ?? 3; 

    $loja_id = (int) $_POST['loja_id'];
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $categoria = mysqli_real_escape_string($conn, $_POST['tipopeca']);
    $estado = mysqli_real_escape_string($conn, $_POST['estado']);
    $detalhes = mysqli_real_escape_string($conn, $_POST['detalhes']);

    $sql = "INSERT INTO avaliacao_peca (consumidor_id, loja_id, nome_peca, categoria, estado, detalhes) 
            VALUES ('$consumidor_id', '$loja_id', '$nome', '$categoria', '$estado', '$detalhes')";

    if (mysqli_query($conn, $sql)) {
        
        echo "<script>alert('Avaliação enviada com sucesso para a loja!'); window.location.href = '../../frontend/home.html';</script>";
    } else {
        echo "Erro ao salvar: " . mysqli_error($conn);
    }
}
?>