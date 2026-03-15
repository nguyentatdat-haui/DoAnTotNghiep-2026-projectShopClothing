<?php
$order = $order ?? null;
$orderId = $order_id ?? 0;
$base = rtrim(base_url(), '/');
?>
<div class="checkout-success-page">
    <div class="shop-container">
        <div class="success-box">
            <h1>Thank you for your order</h1>
            <p class="success-message">Your order has been placed successfully.</p>
            <?php if ($orderId): ?>
                <p class="success-order-id">Order number: <strong>#<?= (int) $orderId ?></strong></p>
            <?php endif; ?>
            <p>We will contact you shortly to confirm delivery.</p>
            <p class="success-actions">
                <a href="<?= $base ?>/products" class="pagination-link">Continue shopping</a>
                <a href="<?= $base ?>" class="pagination-link">Back to home</a>
            </p>
        </div>
    </div>
</div>
