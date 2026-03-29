<?php if (!empty($message)): ?>
<div class="alert alert-<?= $type ?? 'success' ?> alert-dismissible fade show" role="alert">
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

// COMO USAR:
//  $message = "Salvo com sucesso!";
//  $type = "success";  // ou outros como "danger" ou "error"
//  include 'components/alert.php';