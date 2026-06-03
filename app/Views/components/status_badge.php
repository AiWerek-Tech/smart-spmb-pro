<?php
$status = strtolower((string) ($status ?? 'draft'));
$labels = [
    'draft' => 'Draft',
    'submitted' => 'Submitted',
    'verified' => 'Verified',
    'need_revision' => 'Need Revision',
    'accepted' => 'Accepted',
    'rejected' => 'Rejected',
    'dapodik_ready' => 'Dapodik Ready',
    'pending' => 'Pending',
    'approved' => 'Approved',
];
$tones = [
    'draft' => 'muted',
    'submitted' => 'warning',
    'verified' => 'primary',
    'need_revision' => 'danger',
    'accepted' => 'success',
    'rejected' => 'danger',
    'dapodik_ready' => 'info',
    'pending' => 'warning',
    'approved' => 'success',
];
$label = $labels[$status] ?? ucwords(str_replace('_', ' ', $status));
$tone = $tones[$status] ?? 'muted';
?>
<span class="admin-status-badge admin-status-badge--<?= esc($tone) ?>">
    <?= esc($label) ?>
</span>
