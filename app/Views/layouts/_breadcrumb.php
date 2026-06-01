<!-- Breadcrumb Navigation -->
<?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav aria-label="breadcrumb" class="sp-breadcrumb">
    <ol class="breadcrumb mb-0">
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php if ($i === count($breadcrumbs) - 1): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($crumb['title']) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= $crumb['url'] ?>"><?= esc($crumb['title']) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
