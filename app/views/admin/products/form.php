<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$product = $product ?? null;
$categories = $categories ?? [];
$colors = $colors ?? [];
$sizes = $sizes ?? [];
$variants = $variants ?? [];
$isEdit = $product !== null;
$id = $isEdit ? (is_object($product) ? $product->id : $product['id']) : '';
$productImages = $isEdit && !empty($product->images) ? $product->images : [];
?>
<div class="admin-card">
    <h2 style="margin:0 0 24px; font-size:1.25rem; color:#1e293b;"><?= $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm' ?></h2>
    <form method="post" action="<?= $isEdit ? $adminBase . '/products/update/' . $id : $adminBase . '/products/store' ?>" enctype="multipart/form-data">

        <div class="product-form-section">
            <h3 class="product-form-section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</h3>
            <div class="form-group">
                <label for="name">Tên sản phẩm *</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($isEdit ? (is_object($product) ? $product->name : $product['name']) : '') ?>" placeholder="Nhập tên sản phẩm">
            </div>
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" placeholder="Mô tả ngắn về sản phẩm" style="min-height:100px;"><?= htmlspecialchars($isEdit ? (is_object($product) ? $product->description : $product['description']) : '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id">
                    <option value="0">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $c):
                        $cid = is_object($c) ? $c->id : $c['id'];
                        $cname = is_object($c) ? $c->name : $c['name'];
                        $selected = $isEdit && (int)(is_object($product) ? $product->category_id : $product['category_id']) === (int)$cid ? ' selected' : '';
                    ?>
                    <option value="<?= (int)$cid ?>"<?= $selected ?>><?= htmlspecialchars($cname) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="product-form-section">
            <h3 class="product-form-section-title"><i class="fas fa-tag"></i> Giá</h3>
            <div class="product-form-grid-2">
                <div class="form-group">
                    <label for="base_price">Giá gốc</label>
                    <input type="number" id="base_price" name="base_price" step="0.01" min="0" value="<?= $isEdit ? (is_object($product) ? $product->base_price : $product['base_price']) : '0' ?>" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="discount_price">Giá khuyến mãi</label>
                    <input type="number" id="discount_price" name="discount_price" step="0.01" min="0" value="<?= $isEdit ? (is_object($product) ? $product->discount_price : $product['discount_price']) : '' ?>" placeholder="Để trống nếu không giảm giá">
                </div>
            </div>
        </div>

        <div class="product-form-section">
            <h3 class="product-form-section-title"><i class="fas fa-images"></i> Ảnh sản phẩm</h3>

            <div class="form-group">
                <label>Ảnh đại diện (thumb)</label>
                <?php
                $thumbVal = $isEdit ? (is_object($product) ? $product->thumbnail : $product['thumbnail']) : '';
                $thumbSrc = $thumbVal ? (strpos($thumbVal, 'http') === 0 || strpos($thumbVal, '/') === 0 ? $thumbVal : asset($thumbVal)) : '';
                ?>
                <div class="product-thumb-box">
                    <?php if ($thumbSrc): ?>
                    <img src="<?= htmlspecialchars($thumbSrc) ?>" alt="">
                    <?php else: ?>
                    <div class="thumb-placeholder">Chưa có ảnh</div>
                    <?php endif; ?>
                    <div class="product-form-upload-zone">
                        <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="upload-preview"></div>
                    </div>
                </div>
                <input type="text" id="thumbnail" name="thumbnail" value="<?= htmlspecialchars($thumbVal) ?>" placeholder="Hoặc dán URL ảnh" style="margin-top:10px; max-width:400px;">
                <p class="form-hint">JPEG, PNG, GIF, WebP. Tối đa 5MB.</p>
            </div>

            <div class="form-group">
                <label>Ảnh phụ (gallery)</label>
                <?php if (!empty($productImages)): ?>
                <div class="product-gallery">
                    <?php foreach ($productImages as $img):
                        $row = is_array($img) ? $img : (array)$img;
                        $imgId = $row['id'] ?? 0;
                        $imgUrl = $row['image_url'] ?? '';
                        $imgSrc = $imgUrl ? (strpos($imgUrl, 'http') === 0 || strpos($imgUrl, '/') === 0 ? $imgUrl : asset($imgUrl)) : '';
                        $isMain = !empty($row['is_main']);
                    ?>
                    <div class="product-gallery-item">
                        <?php if ($isMain): ?><span class="badge-main">Ảnh chính</span><?php endif; ?>
                        <?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt=""><?php else: ?><div class="thumb-placeholder" style="height:84px;margin:0 auto 8px;"></div><?php endif; ?>
                        <label><input type="checkbox" name="delete_image_ids[]" value="<?= (int)$imgId ?>"> Xóa</label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="product-form-upload-zone" style="margin-top:12px;">
                    <input type="file" name="extra_images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                    <div class="upload-preview"></div>
                </div>
                <p class="form-hint">Chọn nhiều ảnh để thêm vào gallery. Có thể xóa ảnh cũ bằng checkbox phía trên.</p>
            </div>
        </div>

        <div class="product-form-section">
            <h3 class="product-form-section-title"><i class="fas fa-palette"></i> Thuộc tính / Biến thể (màu, size) <span class="required">*</span></h3>
            <p class="form-hint" style="margin-bottom:12px;">Bắt buộc phải có ít nhất một biến thể (chọn Màu hoặc Size). Khách hàng chỉ có thể thêm vào giỏ khi sản phẩm có biến thể. Thêm các biến thể với giá, tồn kho, SKU tùy chọn. Cần có dữ liệu Màu và Size trong database (bảng <code>colors</code>, <code>sizes</code>).</p>
            <?php if (!empty($variants)): ?>
            <table class="admin-table" style="margin-bottom:16px;max-width:720px;">
                <thead>
                    <tr>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>SKU</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($variants as $v): $row = is_array($v) ? $v : (array)$v; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['color_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['size_name'] ?? '—') ?></td>
                        <td><?= $row['price'] !== null && $row['price'] !== '' ? number_format((float)$row['price'], 0, ',', '.') : '—' ?></td>
                        <td><?= $row['stock_quantity'] !== null && $row['stock_quantity'] !== '' ? (int)$row['stock_quantity'] : '—' ?></td>
                        <td><?= htmlspecialchars($row['sku'] ?? '') ?></td>
                        <td><label><input type="checkbox" name="delete_variant_ids[]" value="<?= (int)($row['id'] ?? 0) ?>"> Xóa</label></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            <p style="margin-bottom:8px;font-weight:500;">Thêm biến thể mới:</p>
            <div class="variant-rows">
                <?php for ($i = 0; $i < 3; $i++): ?>
                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 100px 120px;gap:10px;align-items:end;margin-bottom:10px;max-width:720px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Màu</label>
                        <select name="variants[<?= $i ?>][color_id]">
                            <option value="0">-- Chọn màu --</option>
                            <?php foreach ($colors as $col): $cid = is_object($col) ? $col->id : $col['id']; $cname = is_object($col) ? $col->name : $col['name']; ?>
                            <option value="<?= (int)$cid ?>"><?= htmlspecialchars($cname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Size</label>
                        <select name="variants[<?= $i ?>][size_id]">
                            <option value="0">-- Chọn size --</option>
                            <?php foreach ($sizes as $sz): $sid = is_object($sz) ? $sz->id : $sz['id']; $sname = is_object($sz) ? $sz->name : $sz['name']; ?>
                            <option value="<?= (int)$sid ?>"><?= htmlspecialchars($sname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Giá (tùy chọn)</label>
                        <input type="number" name="variants[<?= $i ?>][price]" step="0.01" min="0" placeholder="Dùng giá SP">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Tồn kho</label>
                        <input type="number" name="variants[<?= $i ?>][stock_quantity]" min="0" placeholder="0">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>SKU</label>
                        <input type="text" name="variants[<?= $i ?>][sku]" placeholder="Mã">
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="product-form-section">
            <h3 class="product-form-section-title"><i class="fas fa-toggle-on"></i> Trạng thái & nhãn</h3>
            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" style="max-width:200px;">
                    <option value="active"<?= ($isEdit && (is_object($product) ? $product->status : $product['status']) === 'active') ? ' selected' : '' ?>>Đang bán</option>
                    <option value="inactive"<?= ($isEdit && (is_object($product) ? $product->status : $product['status']) === 'inactive') ? ' selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div class="product-form-grid" style="max-width:400px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 0;">
                    <input type="checkbox" name="is_new" value="1"<?= ($isEdit && (is_object($product) ? $product->is_new : $product['is_new'])) ? ' checked' : '' ?>>
                    <span><i class="fas fa-star" style="color:#f59e0b;"></i> Sản phẩm mới</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 0;">
                    <input type="checkbox" name="is_best_seller" value="1"<?= ($isEdit && (is_object($product) ? $product->is_best_seller : $product['is_best_seller'])) ? ' checked' : '' ?>>
                    <span><i class="fas fa-fire" style="color:#ef4444;"></i> Bán chạy</span>
                </label>
            </div>
        </div>

        <div class="product-form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' ?></button>
            <a href="<?= $adminBase ?>/products" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
