<!-- Skeleton Stat Cards -->
<div class="row">
    <?php for ($i = 0; $i < ($count ?? 4); $i++): ?>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card sp-skeleton-card"
                style="border: 1px solid var(--sp-card-border); background: var(--sp-card-bg); border-radius: var(--sp-radius-md); padding: var(--sp-space-lg); overflow: hidden; position: relative;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="sp-skeleton" style="width: 80px; height: 16px;"></div>
                    <div class="sp-skeleton sp-skeleton-avatar" style="width: 36px; height: 36px; border-radius: 50%;">
                    </div>
                </div>
                <div class="sp-skeleton mb-2" style="width: 120px; height: 32px;"></div>
                <div class="sp-skeleton" style="width: 60px; height: 12px;"></div>
            </div>
        </div>
    <?php endfor; ?>
</div>