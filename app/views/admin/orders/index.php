<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$orders = $orders ?? [];
$pagination = $pagination ?? [];
$total = $pagination['total'] ?? 0;
$current_page = $pagination['current_page'] ?? 1;
$total_pages = $pagination['total_pages'] ?? 1;
?>
<div class="admin-card">
    <h2 style="margin:0 0 16px;">Danh sách đơn hàng</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngay</th>
                <th>Thao tac</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): 
                $id = $o['id'] ?? 0;
                $userName = $o['user_name'] ?? '';
                $userEmail = $o['user_email'] ?? '';
                $totalAmount = $o['total_amount'] ?? 0;
                $status = $o['status'] ?? '';
                $created = $o['created_at'] ?? '';
            ?>
            <tr>
                <td>#<?= (int)$id ?></td>
                <td><?= htmlspecialchars($userName ?: $userEmail ?: '—') ?></td>
                <td><?= number_format((float)$totalAmount, 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($status) ?></td>
                <td><?= htmlspecialchars($created) ?></td>
                <td><a href="<?= $adminBase ?>/orders/<?= (int)$id ?>" class="btn btn-primary btn-sm">Chi tiết</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($orders)): ?>
        <p style="color:#64748b;">Chưa có đơn hàng nào.</p>
    <?php endif; ?>
    <?php if ($total_pages > 1): ?>
        <p style="margin-top:16px;">
            Trang <?= $current_page ?> / <?= $total_pages ?>
            <?php if ($current_page > 1): ?><a href="<?= $adminBase ?>/orders?page=<?= $current_page - 1 ?>">Trước</a><?php endif; ?>
            <?php if ($current_page < $total_pages): ?><a href="<?= $adminBase ?>/orders?page=<?= $current_page + 1 ?>">Sau</a><?php endif; ?>
        </p>
    <?php endif; ?>
</div>
