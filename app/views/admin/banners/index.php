<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$banners = $banners ?? [];
?>
<div class="admin-card">
    <h2 style="margin:0 0 20px;">Cài đặt Banner trang chủ</h2>
    <p style="color:#64748b;margin-bottom:20px;">Upload ảnh cho từng slot. Banner chính và banner giữa hiển thị trên trang chủ. Các slot ad1, ad2, ad3 dùng cho vùng quảng cáo. Định dạng: JPEG, PNG, GIF, WebP. Tối đa 3MB/slot.</p>
    <form method="post" action="<?= $adminBase ?>/banners/save" enctype="multipart/form-data">
        <?php foreach ($banners as $slug => $b): 
            $imgUrl = $b['image_url'] ?? '';
            $imgSrc = $imgUrl ? (strpos($imgUrl, 'http') === 0 || strpos($imgUrl, '/') === 0 ? $imgUrl : asset($imgUrl)) : '';
        ?>
        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin-bottom:16px;">
            <h3 style="margin:0 0 12px;"><?= htmlspecialchars($b['label']) ?> (<?= htmlspecialchars($b['slug']) ?>)</h3>
            <div class="form-group">
                <label>Ảnh banner</label>
                <?php if ($imgSrc): ?>
                <div style="margin-bottom:10px;">
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" style="max-width:320px;max-height:120px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;">
                </div>
                <?php endif; ?>
                <input type="file" name="banner_image[<?= htmlspecialchars($slug) ?>]" accept="image/jpeg,image/png,image/gif,image/webp">
                <div class="upload-preview"></div>
                <input type="hidden" name="image_url[<?= htmlspecialchars($slug) ?>]" value="<?= htmlspecialchars($imgUrl) ?>">
                <p class="form-hint" style="margin-top:4px;">Để trống nếu giữ ảnh hiện tại. Chọn file mới để thay ảnh.</p>
            </div>
            <div class="form-group">
                <label>Link khi click</label>
                <input type="text" name="link_url[<?= htmlspecialchars($slug) ?>]" value="<?= htmlspecialchars($b['link_url']) ?>" placeholder="https://... hoặc /products">
            </div>
            <div class="form-group">
                <label>Alt text</label>
                <input type="text" name="alt_text[<?= htmlspecialchars($slug) ?>]" value="<?= htmlspecialchars($b['alt_text']) ?>" placeholder="Mô tả ảnh">
            </div>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">Lưu tất cả</button>
    </form>
</div>
