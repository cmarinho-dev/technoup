<?php
$title = "Login";
include '../../components/head.php';
include '../../components/navbar.php';

$fields = [
    "email" => "Email",
    "senha" => "Senha"
];
$buttonText = "Entrar";
$action = "";
?>

<div class="container mt-5">
    <?php include '../../components/form.php' ?>
</div>

<?php include '../../components/footer.php'; ?>

<script src="../../../js/_login.js"></script>