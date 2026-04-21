<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card glass">
            <div class="university-logo text-center" style="margin-bottom: 25px;">
                <img src="<?= asset('images/logoHaui.png') ?>" alt="Logo HaUI" style="height: 80px;">
            </div>
            <div class="auth-header text-center">
                <h1 class="auth-title">Đăng Ký</h1>
                <p class="auth-subtitle">Tạo tài khoản để theo dõi đơn hàng của bạn</p>
            </div>
            <div class="auth-body">
                <form action="<?= base_url() ?>/register" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="name" class="form-label">Họ và tên *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-check-double"></i>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="0123 456 789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <div class="input-with-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" id="address" class="form-control" placeholder="Số nhà, đường, quận, thành phố">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Đăng Ký Tài Khoản</button>
                </form>
            </div>
            <div class="auth-footer text-center">
                <p>Đã có tài khoản? <a href="<?= base_url() ?>/login" class="auth-link">Đăng nhập ngay</a></p>
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
        max-width: 500px;
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

    .form-row {
        display: flex;
        gap: 15px;
        width: 100%;
        margin-bottom: 20px;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
        min-width: 0; /* Prevents flex items from overflowing */
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
        font-size: 14px;
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
        margin-top: 10px;
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

    @media (max-width: 576px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
