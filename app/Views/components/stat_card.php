<?php
$label = (string) ($label ?? '');
$value = (int) ($value ?? 0);
$icon = (string) ($icon ?? 'circle');
$tone = (string) ($tone ?? 'primary');
$meta = (string) ($meta ?? '');
$url = (string) ($url ?? '#');
?>
<a href="<?= esc($url) ?>" class="admin-command-card admin-command-card--<?= esc($tone) ?>" data-dashboard-summary-card>
    <span class="admin-command-card__icon" aria-hidden="true">
        <i data-lucide="<?= esc($icon) ?>"></i>
    </span>
    <span class="admin-command-card__body">
        <span class="admin-command-card__label"><?= esc($label) ?></span>
        <span class="admin-command-card__value"><?= number_format($value, 0, ',', '.') ?></span>
        <?php if ($meta !== ''): ?>
            <span class="admin-command-card__meta"><?= esc($meta) ?></span>
        <?php endif; ?>
    </span>
</a>
