<?php
//esta pagina não tem utilidade
//então apenas redirecionará para outra
$content = <<<HTML
<script>
    location.href = "./home/";
</script>
HTML;
require_once './_componentes/layout.php';