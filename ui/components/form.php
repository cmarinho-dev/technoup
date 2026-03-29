<form method="POST" action="<?= $action ?>">
    <?php foreach ($fields as $name => $label): ?>
        <div class="mb-3">
            <label class="form-label"><?= $label ?></label>
            <input 
                type="text" 
                name="<?= $name ?>" 
                class="form-control"
                value="<?= $values[$name] ?? '' ?>"
            >
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">
        <?= $buttonText ?? 'Salvar' ?>
    </button>
</form>
<!-- $action = "salvar.php";
$fields = [
    "nome" => "Nome",
    "email" => "Email"
];
$values = $cliente ?? [];

include 'components/form.php'; -->