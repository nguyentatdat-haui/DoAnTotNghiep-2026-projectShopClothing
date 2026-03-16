<?php
$base = rtrim(base_url(), '/');
$adminBase = $base . '/admin';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>/css/admin-login.css">

<style>
    /* Override layout defaults for full-page centered login */
    .admin-wrap {
        display: block !important;
        background: var(--bg-gradient);
    }
    .admin-main {
        background: transparent !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .admin-content {
        padding: 0 !important;
        flex: none !important;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .flash-admin {
        max-width: 400px;
        margin: 0 auto 20px;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <img src="<?= $base ?>/images/logo.png" alt="Logo">
        </div>

        <?php if (isset($_SESSION['flash']['message'])): ?>
            <?php
            $msg = $_SESSION['flash']['message'];
            $type = $_SESSION['flash']['message_type'] ?? 'info';
            unset($_SESSION['flash']['message'], $_SESSION['flash']['message_type']);
            ?>
            <div class="flash-admin <?= htmlspecialchars($type) ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= $adminBase ?>/login">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required autofocus 
                       placeholder="Nhập email của bạn"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Nhập mật khẩu">
            </div>
            
            <button type="submit" class="btn-login">
                Đăng nhập ngay <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        
        <a href="<?= $base ?>" class="back-to-site">
            <i class="fas fa-chevron-left"></i> Quay lại trang chủ
        </a>
    </div>
</div>

