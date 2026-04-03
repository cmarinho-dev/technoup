<?php
$title = "Alterar Lojista";
include '../../components/head.php';
include '../../components/navbar.php';
?>

<div class="container mt-4">
    <h1>Alterar Lojista</h1>

    <?php
    // formulario
    $action = "../../../php/lojista_alterar.php";
    $method = "POST";
    $fields = [
        "id" => "ID",
        "email" => "Email",
        "senha" => "Senha",
        "cnpj" => "CNPJ",
        "cep_loja" => "CEP",
        "nome_loja" => "Nome da Loja",
        "telefone" => "Telefone",
        "ativo" => "Ativo"
    ];
    $buttonText = "Salvar Alterações";

    include '../../components/form.php';
    ?>

<script src="../../../js/_valida_sessao.js"></script>
<script src="../../../js/lojista_alterar.js"></script>
<?php include '../../components/footer.php'; ?>
