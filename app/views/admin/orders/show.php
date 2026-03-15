<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$order = $order ?? null;
$items = $items ?? [];
$user = $user ?? null;
if (!$order) return;
$oid = is_object($order) ? $order->id : $order['id'];
$totalAmount = is_object($order) ? $order->total_amount : $order['total_amount'];
$status = is_object($order) ? $order->status : $order['status'];
$created = is_object($order) ? $order->created_at : $order['created_at'];
$paymentMethod = is_object($order) ? $order->payment_method : $order['payment_method'];
?>
<div class="admin-card">
    <h2 style="margin:0 0 16px;">Đơn hàng #<?= (int)$oid ?></h2>
    <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($created) ?> | <strong>Thanh toán:</strong> <?= htmlspecialchars($paymentMethod ?? '') ?></p>
    <?php if ($user): ?>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars(is_object($user) ? $user->name : $user['name']) ?> — <?= htmlspecialchars(is_object($user) ? $user->email : $user['email']) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= $adminBase ?>/orders/update-status/<?= (int)$oid ?>" style="margin-bottom:20px;">
        <label for="status">Trạng thái:</label>
        <select name="status" id="status">
            <option value="pending"<?= $status === 'pending' ? ' selected' : '' ?>>Chờ xử lý</option>
            <option value="processing"<?= $status === 'processing' ? ' selected' : '' ?>>Đang xử lý</option>
            <option value="completed"<?= $status === 'completed' ? ' selected' : '' ?>>Hoàn thành</option>
            <option value="cancelled"<?= $status === 'cancelled' ? ' selected' : '' ?>>Đã hủy</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>SKU</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row): 
                $qty = (int)($row['quantity'] ?? 0);
                $price = (float)($row['price'] ?? 0);
                $sub = $qty * $price;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['sku'] ?? '—') ?></td>
                <td><?= $qty ?></td>
                <td><?= number_format($price, 0, ',', '.') ?></td>
                <td><?= number_format($sub, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p style="margin-top:12px;"><strong>Tổng đơn hàng:</strong> <?= number_format((float)$totalAmount, 0, ',', '.') ?></p>
    <a href="<?= $adminBase ?>/orders" class="btn btn-secondary">← Danh sách đơn hàng</a>
</div>
