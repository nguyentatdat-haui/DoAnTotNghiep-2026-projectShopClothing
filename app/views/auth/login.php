<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card glass">
            <div class="university-logo text-center" style="margin-bottom: 25px;">
                <img src="<?= asset('images/logoHaui.png') ?>" alt="Logo HaUI" style="height: 80px;">
            </div>
            <div class="auth-header text-center">
                <h1 class="auth-title">Đăng Nhập</h1>
                <p class="auth-subtitle">Chào mừng bạn quay trở lại với Clothing Shop</p>
            </div>
            <div class="auth-body">
                <form action="<?= base_url() ?>/login" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="form-footer">
                        <!-- <a href="#" class="forgot-password">Quên mật khẩu?</a> -->
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
                </form>
            </div>
            <div class="auth-footer text-center">
                <p>Chưa có tài khoản? <a href="<?= base_url() ?>/register" class="auth-link">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-page {
        min-height: calc(100vh - 100px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .auth-container {
        width: 100%;
        max-width: 450px;
    }

    .glass {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    .auth-card {
        border-radius: 20px;
        padding: 40px;
    }

    .auth-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 28px;
        margin-bottom: 8px;
        color: #111;
    }

    .auth-subtitle {
        color: #666;
        font-size: 14px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-with-icon {
        position: relative;
        width: 100%;
    }

    .input-with-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
        z-index: 2;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px 12px 42px;
        background: #f8f9fc;
        border: 1px solid #e1e1e1;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        outline: none;
        box-sizing: border-box;
        color: #333;
    }

    .form-control:focus {
        background: #fff;
        border-color: #d4af37;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .forgot-password {
        font-size: 13px;
        color: #666;
        text-decoration: none;
    }

    .btn-primary {
        background: #111;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-primary:hover {
        background: #333;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .auth-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(0,0,0,0.05);
        font-size: 14px;
        color: #666;
    }

    .auth-link {
        color: #d4af37;
        font-weight: 600;
        text-decoration: none;
    }

    .auth-link:hover {
        text-decoration: underline;
    }

    .text-center { text-align: center; }
</style>
