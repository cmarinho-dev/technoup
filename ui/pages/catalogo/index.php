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
        "tipo" => $row['tipo']
    ];
}
?>

<div class="container mt-5">

    <h2 class="mb-4">Meu Catálogo</h2>

    <?php
    // formulário
    $action = "../../../php/catalogo_salvar.php";
    $method = "POST";
    $fields = [
        "nome" => "Nome do Produto",
        "preco" => "Preço",
        "tipo" => "Tipo"
    ];
    $buttonText = "Adicionar Produto";

    include '../../components/form.php';
    ?>

    <hr>

    <?php
    // tabela
    $columns = ["ID", "Nome", "Preço", "Tipo"];
    include '../../components/table.php';
    ?>

</div>

<script src="../../../js/_valida_sessao.js"></script>
<script>valida_sessao()</script>

<script>
function buscar(id) {
    window.location.href = "alterar.php?id=" + encodeURIComponent(id);
}

async function excluir(id) {
    const confirmar = confirm("Tem certeza que deseja excluir?");
    if (!confirmar) return;

    const retorno = await fetch("../../../php/catalogo_excluir.php?id=" + encodeURIComponent(id));
    const resposta = await retorno.json();

    if (resposta.status === "ok") {
        location.reload();
    } else {
        alert("Erro: " + resposta.mensagem);
    }
}
</script>

<?php include '../../components/footer.php'; ?>