<?php
$title = "Login";
include '../components/head.php';
include '../components/navbar.php';

$action = "../../php/cliente_login.php";
$fields = [
    "usuario" => "Usuário",
    "senha" => "Senha"
];
$buttonText = "Entrar";
?>
<div class="container mt-5">
    <?php include '../components/form.php' ?>
</div>

<?php include '../components/footer.php'; ?>