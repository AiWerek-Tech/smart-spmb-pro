<!-- Skeleton Table Loader -->
<div class="table-responsive">
    <table class="table" style="margin-bottom: 0;">
        <thead>
            <tr>
                <?php for ($c = 0; $c < ($cols ?? 5); $c++): ?>
                    <th style="border-bottom: 2px solid var(--sp-border-color); padding: var(--sp-space-md) var(--sp-space-lg);">
                        <div class="sp-skeleton" style="width: <?= rand(60, 100) ?>px; height: 16px;"></div>
                    </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($r = 0; $r < ($rows ?? 5); $r++): ?>
                <tr style="border-bottom: 1px solid var(--sp-border-color);">
                    <?php for ($c = 0; $c < ($cols ?? 5); $c++): ?>
                        <td style="padding: var(--sp-space-md) var(--sp-space-lg); vertical-align: middle;">
                            <div class="sp-skeleton" style="width: <?= rand(40, 130) ?>px; height: 14px; border-radius: var(--sp-radius-sm);"></div>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>
