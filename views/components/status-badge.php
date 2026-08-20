<?php
$status = $status ?? 'pending';
$classes = match($status) {
    'pending' => 'badge-pending',
    'approved' => 'badge-approved',
    'rejected' => 'badge-rejected',
    'completed' => 'badge-completed',
    'cancelled' => 'badge-cancelled',
    'paid' => 'badge-paid',
    'active' => 'badge-approved',
    'inactive' => 'badge-cancelled',
    'available' => 'badge-approved',
    'full' => 'badge-pending',
    default => 'badge-pending',
};
?>
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium <?= $classes ?>">
    <?= e(ucfirst($status)) ?>
</span>
