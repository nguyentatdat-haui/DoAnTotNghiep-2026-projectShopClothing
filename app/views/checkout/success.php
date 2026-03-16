<?php
$order = $order ?? null;
$orderId = $order_id ?? 0;
$base = rtrim(base_url(), '/');
?>
<div class="checkout-success-page">
    <div class="shop-container">
        <div class="success-box">
            <h1>Cảm ơn bạn đã đặt hàng</h1>
            <p class="success-message">Đơn hàng của bạn đã được tạo thành công.</p>
            <?php if ($orderId): ?>
                <p class="success-order-id">Mã đơn hàng: <strong>#<?= (int) $orderId ?></strong></p>
            <?php endif; ?>
            <p>Chúng tôi sẽ liên hệ sớm để xác nhận giao hàng.</p>
            <p class="success-actions">
                <a href="<?= $base ?>/products" class="pagination-link">Tiếp tục mua sắm</a>
                <a href="<?= $base ?>" class="pagination-link">Về trang chủ</a>
            </p>
        </div>
    </div>
</div>
