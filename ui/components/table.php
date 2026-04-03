<table class="table table-striped">
    <thead>
        <tr>
            <?php foreach ($columns as $col): ?>
                <th><?= $col ?></th>
            <?php endforeach; ?>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?= $value ?></td>
                <?php endforeach; ?>
                <td>
                    <button type="button" onclick="buscar(<?= $row['id'] ?>)" class="btn btn-sm btn-warning">
                        Editar
                    </button>

                    <button type="button" onclick="excluir(<?= $row['id'] ?>)" class="btn btn-sm btn-danger">
                        Excluir
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>