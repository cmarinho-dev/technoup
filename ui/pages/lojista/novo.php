<?php
$title = "Novo Lojista";
include '../../components/head.php';
include '../../components/navbar.php';
?>

<div class="container mt-4">
    <h1>Novo Lojista</h1>

<?php
$fields = [
    "email" => "Email",
    "senha" => "Senha",
    "cnpj" => "CNPJ",
    "cep_loja" => "CEP",
    "nome_loja" => "Nome da Loja",
    "telefone" => "Telefone",
    "ativo" => "Ativo"
];
    $buttonText = "Salvar Novo Lojista";

    include '../../components/form.php';
    ?>

<script src="../../../js/_valida_sessao.js"></script>
<script src="../../../js/lojista_novo.js"></script>
<?php include '../../components/footer.php'; ?>
