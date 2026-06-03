<?php
$icon = (string) ($icon ?? 'inbox');
$title = (string) ($title ?? 'Belum ada data');
$text = (string) ($text ?? 'Data akan muncul setelah aktivitas tersedia.');
?>
<div class="admin-empty-state">
    <span class="admin-empty-state__icon" aria-hidden="true">
        <i data-lucide="<?= esc($icon) ?>"></i>
    </span>
    <p class="admin-empty-state__title"><?= esc($title) ?></p>
    <p class="admin-empty-state__text"><?= esc($text) ?></p>
</div>
