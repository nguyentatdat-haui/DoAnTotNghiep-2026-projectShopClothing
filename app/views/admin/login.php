<?php
$base = rtrim(base_url(), '/');
$adminBase = $base . '/admin';
?>
<div class="login-box">
    <h2><i class="fas fa-lock"></i> Đăng nhập Admin</h2>
    <form method="post" action="<?= $adminBase ?>/login">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
    </form>
</div>
