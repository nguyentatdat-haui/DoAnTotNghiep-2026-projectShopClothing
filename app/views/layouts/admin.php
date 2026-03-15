<?php
$base = rtrim(base_url(), '/');
$adminBase = $base . '/admin';
$showSidebar = isset($show_sidebar) ? $show_sidebar : true;
$title = $title ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; }
        .admin-wrap { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 240px; background: #1e293b; color: #e2e8f0; flex-shrink: 0; }
        .admin-sidebar a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px 16px; transition: background .2s, color .2s; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: #334155; color: #fff; }
        .admin-sidebar .brand { padding: 20px 16px; font-weight: 700; color: #fff; border-bottom: 1px solid #334155; }
        .admin-main { flex: 1; display: flex; flex-direction: column; }
        .admin-top { background: #fff; padding: 16px 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); display: flex; justify-content: space-between; align-items: center; }
        .admin-top h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .admin-top .logout { color: #64748b; font-size: 0.9rem; }
        .admin-top .logout:hover { color: #ef4444; }
        .admin-content { padding: 24px; flex: 1; }
        .admin-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 20px; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #64748b; color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        .flash-admin { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
        .flash-admin.success { background: #dcfce7; color: #166534; }
        .flash-admin.error { background: #fee2e2; color: #991b1b; }
        .flash-admin.warning { background: #fef3c7; color: #92400e; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #374151; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; }
        .form-group textarea { min-height: 100px; }
        .login-box { max-width: 380px; margin: 60px auto; padding: 32px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,.08); }
        .login-box h2 { margin: 0 0 24px; font-size: 1.25rem; }
        /* Product form */
        .product-form-section { margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0; }
        .product-form-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .product-form-section-title { font-size: 0.95rem; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
        .product-form-section-title i { color: #64748b; }
        .product-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .product-form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; max-width: 480px; }
        @media (max-width: 600px) { .product-form-grid-2 { grid-template-columns: 1fr; } }
        .product-thumb-box { width: 160px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; text-align: center; }
        .product-thumb-box img { width: 100%; height: 140px; object-fit: cover; border-radius: 6px; display: block; margin-bottom: 10px; }
        .product-thumb-box .thumb-placeholder { width: 100%; height: 140px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 12px; margin-bottom: 10px; }
        .product-gallery { display: flex; flex-wrap: wrap; gap: 12px; }
        .product-gallery-item { width: 100px; padding: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; transition: border-color .2s, box-shadow .2s; }
        .product-gallery-item:hover { border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .product-gallery-item img { width: 84px; height: 84px; object-fit: cover; border-radius: 6px; display: block; margin: 0 auto 8px; }
        .product-gallery-item .badge-main { font-size: 10px; background: #2563eb; color: #fff; padding: 2px 6px; border-radius: 4px; margin-bottom: 6px; }
        .product-gallery-item label { font-size: 12px; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; }
        .product-gallery-item label input { width: auto; max-width: none; margin: 0; }
        .product-form-upload-zone { padding: 16px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-top: 10px; max-width: 400px; }
        .product-form-upload-zone input[type="file"] { width: 100%; max-width: none; }
        .product-form-actions { display: flex; align-items: center; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .product-form-actions .btn { max-width: none; }
        .form-hint { font-size: 12px; color: #64748b; margin-top: 6px; }
        .form-group input[type="number"], .form-group input[type="text"] { max-width: 100%; }
        .product-form-section .form-group input, .product-form-section .form-group select, .product-form-section .form-group textarea { max-width: 100%; }
        .upload-preview { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start; }
        .upload-preview img { max-width: 140px; max-height: 140px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 6px; }
        .upload-preview-label { font-size: 12px; color: #64748b; width: 100%; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="admin-wrap">
    <?php if ($showSidebar): ?>
    <aside class="admin-sidebar">
        <div class="brand"><i class="fas fa-cog"></i> Admin</div>
        <a href="<?= $adminBase ?>" class="<?= (isset($current_page) && $current_page === 'dashboard') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="<?= $adminBase ?>/products" class="<?= (isset($current_page) && $current_page === 'products') ? 'active' : '' ?>"><i class="fas fa-box"></i> Sản phẩm</a>
        <a href="<?= $adminBase ?>/orders" class="<?= (isset($current_page) && $current_page === 'orders') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
        <a href="<?= $adminBase ?>/banners" class="<?= (isset($current_page) && $current_page === 'banners') ? 'active' : '' ?>"><i class="fas fa-image"></i> Banner</a>
        <a href="<?= $base ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Xem site</a>
        <a href="<?= $adminBase ?>/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </aside>
    <?php endif; ?>
    <div class="admin-main">
        <?php if ($showSidebar): ?>
        <header class="admin-top">
            <h1><?= htmlspecialchars($title) ?></h1>
            <a href="<?= $adminBase ?>/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </header>
        <?php endif; ?>
        <main class="admin-content">
            <?php
            if (isset($_SESSION['flash']['message'])) {
                $msg = $_SESSION['flash']['message'];
                $type = $_SESSION['flash']['message_type'] ?? 'info';
                unset($_SESSION['flash']['message'], $_SESSION['flash']['message_type']);
                echo '<div class="flash-admin ' . htmlspecialchars($type) . '">' . htmlspecialchars($msg) . '</div>';
            }
            ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
<script>
(function() {
    function initUploadPreview() {
        var content = document.querySelector('.admin-content');
        if (!content) return;
        content.querySelectorAll('input[type=file][accept*="image"]').forEach(function(input) {
            if (input.dataset.previewInit) return;
            input.dataset.previewInit = '1';
            var container = input.nextElementSibling && input.nextElementSibling.classList.contains('upload-preview')
                ? input.nextElementSibling : null;
            if (!container) {
                container = document.createElement('div');
                container.className = 'upload-preview';
                input.parentNode.insertBefore(container, input.nextSibling);
            }
            var oldUrls = [];
            input.addEventListener('change', function() {
                oldUrls.forEach(function(u) { try { URL.revokeObjectURL(u); } catch(e) {} });
                oldUrls = [];
                container.innerHTML = '';
                var files = this.files;
                if (!files || files.length === 0) return;
                var label = document.createElement('span');
                label.className = 'upload-preview-label';
                label.textContent = 'Preview trước khi lưu:';
                container.appendChild(label);
                for (var i = 0; i < files.length; i++) {
                    if (!files[i].type || files[i].type.indexOf('image/') !== 0) continue;
                    var url = URL.createObjectURL(files[i]);
                    oldUrls.push(url);
                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = 'Preview';
                    container.appendChild(img);
                }
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUploadPreview);
    } else {
        initUploadPreview();
    }
})();
</script>
</body>
</html>
