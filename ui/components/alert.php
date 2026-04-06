<?php if (!empty($message)): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-bg-<?= $type ?? 'success' ?> border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body py-2 px-3 small">
                <?= $message ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<!--
$message = "Salvo com sucesso!";
$type = "success"; // success, danger, warning, info
include '../components/toast.php'; -->