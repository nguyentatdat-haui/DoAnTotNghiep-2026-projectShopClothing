<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$products = $products ?? [];
$pagination = $pagination ?? [];
$total = $pagination['total'] ?? 0;
$current_page = $pagination['current_page'] ?? 1;
$total_pages = $pagination['total_pages'] ?? 1;
?>
<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Danh sách sản phẩm</h2>
        <a href="<?= $adminBase ?>/products/create" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm sản phẩm</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): 
                $thumb = is_object($p) ? ($p->thumbnail ?? '') : ($p['thumbnail'] ?? '');
                $imgSrc = $thumb ? (strpos($thumb, 'http') === 0 || strpos($thumb, '/') === 0 ? $thumb : asset($thumb)) : '';
                $name = is_object($p) ? $p->name : $p['name'];
                $basePrice = is_object($p) ? ($p->base_price ?? 0) : ($p['base_price'] ?? 0);
                $status = is_object($p) ? ($p->status ?? '') : ($p['status'] ?? '');
                $id = is_object($p) ? $p->id : $p['id'];
            ?>
            <tr>
                <td><?= (int)$id ?></td>
                <td><?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt="" style="width:50px;height:50px;object-fit:cover;"><?php else: ?>—<?php endif; ?></td>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= number_format((float)$basePrice, 0, ',', '.') ?>đ</td>
                <td><span class="status-badge <?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></span></td>
                <td>
                    <a href="<?= $adminBase ?>/products/edit/<?= (int)$id ?>" class="btn btn-secondary btn-sm">Sửa</a>
                    <form method="post" action="<?= $adminBase ?>/products/delete/<?= (int)$id ?>" style="display:inline;" onsubmit="return confirm('Xóa sản phẩm này?');">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($products)): ?>
        <p style="color:#64748b;">Chưa có sản phẩm nào.</p>
    <?php endif; ?>
    <?php if ($total_pages > 1): ?>
        <p style="margin-top:16px;">
            Trang <?= $current_page ?> / <?= $total_pages ?> (<?= $total ?> sản phẩm)
            <?php if ($current_page > 1): ?><a href="<?= $adminBase ?>/products?page=<?= $current_page - 1 ?>">Trước</a><?php endif; ?>
            <?php if ($current_page < $total_pages): ?><a href="<?= $adminBase ?>/products?page=<?= $current_page + 1 ?>">Sau</a><?php endif; ?>
        </p>
    <?php endif; ?>
</div>
