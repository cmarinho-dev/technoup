<?php
$title = "Alterar Produto";
include '../../components/head.php';
include '../../components/navbar.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Alterar Produto</h2>

    <?php
    $action = "../../../php/catalogo_alterar.php";
    $method = "POST";
    $fields = [
        "id" => "ID",
        "nome" => "Nome do Produto",
        "preco" => "Preço",
        "tipo" => "Tipo"
    ];
    $buttonText = "Salvar Alterações";

    include '../../components/form.php';
    ?>
</div>

<script src="../../../js/_valida_sessao.js"></script>
<script src="../../../js/catalogo_alterar.js"></script>
<?php include '../../components/footer.php'; ?>
