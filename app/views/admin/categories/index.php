<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$categories = $categories ?? [];
?>
<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Danh sách danh mục</h2>
        <a href="<?= $adminBase ?>/categories/create" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm danh mục</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Danh mục cha</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): 
                $id = is_object($c) ? $c->id : $c['id'];
                $name = is_object($c) ? $c->name : $c['name'];
                $parentId = is_object($c) ? ($c->parent_id ?? 0) : ($c['parent_id'] ?? 0);
                $createdAt = is_object($c) ? ($c->created_at ?? '') : ($c['created_at'] ?? '');
                
                $parentName = '—';
                if ($parentId > 0) {
                    foreach ($categories as $pc) {
                        $pcId = is_object($pc) ? $pc->id : $pc['id'];
                        if ($pcId == $parentId) {
                            $parentName = is_object($pc) ? $pc->name : $pc['name'];
                            break;
                        }
                    }
                }
            ?>
            <tr>
                <td><?= (int)$id ?></td>
                <td><strong><?= htmlspecialchars($name) ?></strong></td>
                <td><?= htmlspecialchars($parentName) ?></td>
                <td><?= htmlspecialchars($createdAt) ?></td>
                <td>
                    <a href="<?= $adminBase ?>/categories/edit/<?= (int)$id ?>" class="btn btn-secondary btn-sm">Sửa</a>
                    <form method="post" action="<?= $adminBase ?>/categories/delete/<?= (int)$id ?>" style="display:inline;" onsubmit="return confirm('Xóa danh mục này?');">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($categories)): ?>
        <p style="color:#64748b;">Chưa có danh mục nào.</p>
    <?php endif; ?>
</div>
