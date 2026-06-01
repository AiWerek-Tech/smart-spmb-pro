<!-- Empty State Component -->
<?php 
    $emptyIcon = $emptyIcon ?? 'inbox';
    $emptyTitle = $emptyTitle ?? 'Belum Ada Data';
    $emptyMessage = $emptyMessage ?? 'Data yang Anda cari belum tersedia saat ini.';
    $emptyActionUrl = $emptyActionUrl ?? null;
    $emptyActionText = $emptyActionText ?? 'Tambah Data';
?>
<div class="sp-empty-state animate-fade-in">
    <div class="sp-empty-state-icon">
        <i data-lucide="<?= esc($emptyIcon) ?>"></i>
    </div>
    <h5 class="sp-empty-state-title"><?= esc($emptyTitle) ?></h5>
    <p class="sp-empty-state-text"><?= esc($emptyMessage) ?></p>
    <?php if ($emptyActionUrl): ?>
        <a href="<?= $emptyActionUrl ?>" class="btn btn-primary btn-sm">
            <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>
            <?= esc($emptyActionText) ?>
        </a>
    <?php endif; ?>
</div>
