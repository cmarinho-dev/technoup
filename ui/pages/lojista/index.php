<?php
$title = "Lojistas";
include '../../components/head.php';
include '../../components/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h1>Lista de Lojistas</h1>
        <div>
            <button id="novo" class="btn btn-primary">Novo</button>
            <button id="logoff" class="btn btn-secondary">Logoff</button>
        </div>
    </div>

    <div id="lista"></div>
</div>

<script src="../../../js/_valida_sessao.js"></script>
<script src="../../../js/lojista_listar.js"></script>
<?php include '../../components/footer.php'; ?>
