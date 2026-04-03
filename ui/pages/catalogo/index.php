
<?php
$title = "Catálogo da Loja";
include '../../components/head.php';
include '../../components/navbar.php';

include '../../../php/_conexao.php';

$sql = "SELECT * FROM produto";
$result = $conexao->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => $row['id'],
        "nome" => $row['nome'],
        "preco" => $row['preco'],
        "tipo" => $row['tipo'],
        "descricao" => $row['descricao'],
        "modelo" => $row['modelo'],
        "marca" => $row['marca'],
        "capacidade" => $row['capacidade'],
        "tipodamemoria" => $row['tipodamemoria'],
        "conector" => $row['conector']
    ];
}
?>

<div class="container mt-5">

    <h2 class="mb-4">Meu Catálogo</h2>

    <?php

// formulario
    $action = "../../../php/catalogo_salvar.php";
    $method = "POST";
    $fields = [
        "nome" => "Nome do Produto",
        "preco" => "Preço",
        "tipo" => "Tipo",
        "descricao" => "Descrição",
        "modelo" => "Modelo",
        "marca" => "Marca",
        "capacidade" => "Capacidade da Memória",
        "tipodamemoria" => "Tipo da Memória",
        "conector" => "Conector"
    ];
    $buttonText = "Adicionar Produto";

    include '../../components/form.php';
    ?>

    <hr>

    <?php
    // tabela
    $columns = ["ID", "Nome", "Preço", "Tipo", "Descrição", "Modelo", "Marca", "Capacidade", "TipodaMemória", "Conector"];
    include '../../components/table.php';
    ?>

</div>
<script src="../../../js/_valida_sessao.js"></script>
<script>valida_sessao()</script>
<?php include '../../components/footer.php'; ?>