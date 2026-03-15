<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$totalProducts = $total_products ?? 0;
$totalOrders = $total_orders ?? 0;
?>
<div class="admin-card">
    <h2 style="margin:0 0 16px">Tổng quan</h2>
    <div style="display:flex;gap:24px;flex-wrap:wrap;">
        <div style="padding:20px;background:#eff6ff;border-radius:8px;min-width:160px;">
            <div style="font-size:0.9rem;color:#1e40af;">Sản phẩm</div>
            <div style="font-size:1.75rem;font-weight:700;color:#1e3a8a;"><?= (int)$totalProducts ?></div>
            <a href="<?= $adminBase ?>/products" class="btn btn-primary btn-sm" style="margin-top:8px;">Quản lý</a>
        </div>
        <div style="padding:20px;background:#f0fdf4;border-radius:8px;min-width:160px;">
            <div style="font-size:0.9rem;color:#166534;">Đơn hàng</div>
            <div style="font-size:1.75rem;font-weight:700;color:#14532d;"><?= (int)$totalOrders ?></div>
            <a href="<?= $adminBase ?>/orders" class="btn btn-primary btn-sm" style="margin-top:8px;">Xem đơn hàng</a>
        </div>
    </div>
</div>
