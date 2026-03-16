<?php
$base = rtrim(base_url(), '/');
$adminBase = $base . '/admin';
$showSidebar = isset($show_sidebar) ? $show_sidebar : true;
$title = $title ?? 'Quản trị';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Nguyễn Tất Đạt-Haui-Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #d4af37; /* Gold */
            --primary-dark: #b8962e;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --content-bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: var(--content-bg); 
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Layout Structure */
        .admin-wrap { display: flex; min-height: 100vh; }

        /* Premium Sidebar */
        .admin-sidebar { 
            width: 280px; 
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff; 
            flex-shrink: 0; 
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .admin-sidebar .brand { 
            padding: 35px 25px; 
            font-family: 'Outfit', sans-serif;
            font-weight: 800; 
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: #fff; 
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .admin-sidebar .brand i {
            color: var(--primary);
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.4));
        }

        .sidebar-menu {
            padding: 25px 15px;
            flex-grow: 1;
        }

        .sidebar-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            margin: 25px 0 15px;
            padding-left: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0.8;
        }

        .sidebar-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.05);
            margin-right: 15px;
        }

        .admin-sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 14px 20px; 
            border-radius: 12px;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
        }

        .admin-sidebar a i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .admin-sidebar a:hover { 
            background: var(--sidebar-hover); 
            color: #fff; 
            transform: translateX(5px);
        }

        .admin-sidebar a.active { 
            background: linear-gradient(90deg, var(--primary) 0%, #eab308 100%);
            color: #0f172a; 
            font-weight: 700;
            box-shadow: 0 10px 15px -3px rgba(212, 175, 55, 0.3);
        }
        
        .admin-sidebar a.active i { color: #0f172a; }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-footer .logout {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
        }

        .sidebar-footer .logout:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Main Content Area */
        .admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

        /* Glassmorphism Header */
        .admin-top { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(10px);
            padding: 20px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 90;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .admin-top h1 { 
            margin: 0; 
            font-size: 1.25rem; 
            font-weight: 800; 
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info { text-align: right; }
        .user-name { display: block; font-weight: 700; font-size: 14px; }
        .user-role { display: block; font-size: 12px; color: var(--text-muted); }

        .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        /* Content Animations */
        .admin-content { 
            padding: 40px; 
            flex: 1; 
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Optimized Cards */
        .admin-card { 
            background: #fff; 
            border-radius: 16px; 
            box-shadow: var(--card-shadow); 
            padding: 30px; 
            margin-bottom: 30px; 
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Global Table Styles */
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th { text-align: left; padding: 16px 12px; font-weight: 700; color: var(--text-muted); border-bottom: 2px solid #f1f5f9; }
        td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main); font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; 
            font-family: inherit; font-size: 14px; transition: var(--transition);
        }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1); outline: none; }

        /* Custom Select / Combo Box for Admin */
        .select-wrapper {
            position: relative;
            display: inline-block;
            min-width: 220px;
        }

        .admin-select {
            appearance: none !important;
            -webkit-appearance: none !important;
            width: 100% !important;
            padding: 12px 40px 12px 20px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            cursor: pointer;
            transition: var(--transition);
        }

        .admin-select:focus {
            border-color: var(--primary) !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
            outline: none;
        }

        .select-wrapper i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            pointer-events: none;
            font-size: 14px;
        }

        .status-update-container {
            background: #f8fafc;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
            border: 1px solid #f1f5f9;
        }

        .form-group-inline {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group-inline label {
            font-weight: 700;
            color: #475569;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Flash Messages */
        .flash-admin { padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .flash-admin.success { background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981; }
        .flash-admin.error { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* Buttons & Forms */
        .btn { 
            display: inline-flex; 
            align-items: center;
            gap: 8px;
            padding: 10px 24px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600;
            cursor: pointer; 
            border: none; 
            transition: var(--transition);
        }

        .btn-primary { 
            background: linear-gradient(135deg, var(--primary) 0%, #eab308 100%); 
            color: #0f172a; 
            box-shadow: 0 4px 14px 0 rgba(212, 175, 55, 0.39);
        }

        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45);
        }

        .btn-secondary { 
            background: #f1f5f9; 
            color: #475569; 
        }

        .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }

        .btn-danger { background: #fee2e2; color: #ef4444; }
        .btn-danger:hover { background: #ef4444; color: #fff; }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.active, .status-badge.completed, .status-badge.success {
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-badge.inactive, .status-badge.cancelled, .status-badge.error {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-badge.pending, .status-badge.warning {
            background: #fffbeb;
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-sidebar { width: 80px; }
            .admin-sidebar .brand span, 
            .admin-sidebar a span,
            .sidebar-label { display: none; }
            .admin-sidebar .brand { padding: 25px 0; justify-content: center; }
            .admin-sidebar a { justify-content: center; padding: 15px; }
        }
    </style>
</head>
<body>
<div class="admin-wrap">
    <?php if ($showSidebar): ?>
    <aside class="admin-sidebar">
        <div class="brand">
            <i class="fas fa-crown"></i> 
            <span>CLOTHING SHOP</span>
        </div>
        
        <div class="sidebar-menu">
            <div class="sidebar-label">Menu</div>
            <a href="<?= $adminBase ?>" class="<?= (isset($current_page) && $current_page === 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a href="<?= $adminBase ?>/categories" class="<?= (isset($current_page) && $current_page === 'categories') ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> <span>Danh mục</span>
            </a>
            <a href="<?= $adminBase ?>/products" class="<?= (isset($current_page) && $current_page === 'products') ? 'active' : '' ?>">
                <i class="fas fa-gem"></i> <span>Sản phẩm</span>
            </a>
            <a href="<?= $adminBase ?>/orders" class="<?= (isset($current_page) && $current_page === 'orders') ? 'active' : '' ?>">
                <i class="fas fa-shopping-bag"></i> <span>Đơn hàng</span>
            </a>
            
            <div class="sidebar-label">Hệ thống</div>
            <a href="<?= $base ?>" target="_blank">
                <i class="fas fa-eye"></i> <span>Xem website</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="<?= $adminBase ?>/logout" class="logout" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');">
                <i class="fas fa-power-off"></i> <span>Đăng xuất</span>
            </a>
        </div>
    </aside>
    <?php endif; ?>

    <div class="admin-main">
        <?php if ($showSidebar): ?>
        <header class="admin-top">
            <h1><?= htmlspecialchars($title) ?></h1>
            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'Administrator') ?></span>
                    <span class="user-role">Nguyễn Tất Đạt-Haui-Admin</span>
                </div>
                <div class="avatar">AD</div>
            </div>
        </header>
        <?php endif; ?>

        <main class="admin-content">
            <?php
            if (!empty($showSidebar) && isset($_SESSION['flash']['message'])) {
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
                label.textContent = 'Xem trước trước khi lưu:';
                container.appendChild(label);
                for (var i = 0; i < files.length; i++) {
                    if (!files[i].type || files[i].type.indexOf('image/') !== 0) continue;
                    var url = URL.createObjectURL(files[i]);
                    oldUrls.push(url);
                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = 'Xem trước';
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
