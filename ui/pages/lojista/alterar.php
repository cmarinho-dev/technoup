<?php
$title = "Alterar Lojista";
include '../../components/head.php';
include '../../components/navbar.php';
?>

<div class="container mt-4">
    <h1>Alterar Lojista</h1>

    <?php
    // formulario
    //$action = "../../../php/lojista_alterar.php";
    // $method = "POST";
    $fields = [
        "id" => "ID",
        "nome_loja" => "Nome da Loja",
        "logradouro" => "Logradouro",
        "nome_lojista" => "Nome do Lojista",
        "cpf" => "CPF",
        "cnpj" => "CNPJ",
        "cep_lojista" => "CEP do Lojista",
        "estado" => "Estado",
        "cidade" => "Cidade",
        "bairro" => "Bairro",
        "numero" => "Número",
        "genero" => "Gênero",
        "email" => "Email",
        "senha" => "Senha",
        "telefone" => "Telefone",
        "ativo" => "Ativo"
    ];
    $buttonText = "Salvar Alterações";

    include '../../components/form.php';
    ?>

<script src="../../../js/_valida_sessao.js"></script>
<script src="../../../js/lojista_alterar.js"></script>
<?php include '../../components/footer.php'; ?>
