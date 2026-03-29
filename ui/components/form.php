<!-- $action = "salvar.php";
$fields = [
    "nome" => "Nome",
    "email" => "Email"
];
-- adicionar values quando for UPDATE
$values = $cliente ?? [];

include '../components/form.php'; -->
<form id="form" method="<?= $method ?? 'DIALOG' ?>" action="<?= $action ?? '' ?>">
    <?php foreach ($fields as $name => $label): ?>
        <div class="mb-3">
            <?= getLabelAndInput($name, $label, $values ?? []) ?>
        </div>
    <?php endforeach; ?>
    <button id="enviar" type="submit" class="btn btn-primary">
        <?= $buttonText ?? 'Salvar' ?>
    </button>
</form>


<?php
function getLabelAndInput($name, $label, $values) {
    $value = $values[$name] ?? '';

    if ($name === 'id') {
        return "<input id=\"id\" type=\"hidden\" name=\"$name\" value=\"$value\">";
    }

    $type = 'text';
    $required = '';
    if ($name === 'senha') {
        $type = 'password';
        $required = 'required';
    } elseif ($name === 'email') {
        $type = 'email';
        $required = 'required';
    }

    return "
        <div class=\"mb-3\">
            <label for=\"$name\" class=\"form-label\">$label</label>
            <input id=\"$name\" type=\"$type\" name=\"$name\" class=\"form-control\" value=\"$value\" $required>
        </div>
    ";
}