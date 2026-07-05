<?php
/**
 * Status Badge Component
 * Usage: <?php include APP_PATH . '/views/components/status-badge.php'; ?>
 * Requires: $status (string), optional $size (sm|md|lg), optional $icon (bool)
 */
?>
<span class="<?php echo getStatusBadgeClass($status); ?> px-3 py-1 rounded-full text-xs font-bold uppercase inline-flex items-center gap-2">
    <?php if ($icon ?? false): ?>
        <i class="fas <?php echo getStatusIcon($status); ?>"></i>
    <?php endif; ?>
    <?php echo htmlspecialchars($status); ?>
</span>
