<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$isEdit = isset($category);
$action = $isEdit ? "$adminBase/categories/update/{$category->id}" : "$adminBase/categories/store";
$categories = $categories ?? [];
?>
<div class="admin-card">
    <h2 style="margin:0 0 20px;"><?= $isEdit ? 'Sửa danh mục' : 'Thêm danh mục mới' ?></h2>
    
    <form action="<?= $action ?>" method="POST">
        <div class="form-group">
            <label for="name">Tên danh mục</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($isEdit ? $category->name : '') ?>" required placeholder="Ví dụ: Áo Nam, Váy Nữ...">
        </div>
        
        <div class="form-group">
            <label for="parent_id">Danh mục cha</label>
            <select name="parent_id" id="parent_id">
                <option value="0">Không có (Danh mục gốc)</option>
                <?php foreach ($categories as $c): 
                    $cId = is_object($c) ? $c->id : $c['id'];
                    $cName = is_object($c) ? $c->name : $c['name'];
                    
                    // Don't show current category in parent list if editing
                    if ($isEdit && $cId == $category->id) continue;
                    
                    $selected = ($isEdit && $category->parent_id == $cId) ? 'selected' : '';
                ?>
                    <option value="<?= $cId ?>" <?= $selected ?>><?= htmlspecialchars($cName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top:30px;display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Cập nhật danh mục' : 'Lưu danh mục' ?>
            </button>
            <a href="<?= $adminBase ?>/categories" class="btn btn-secondary">Hủy bỏ</a>
        </div>
    </form>
</div>
