<?php $base = rtrim(base_url(), '/'); ?>
<footer class="main-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= $base ?>" class="footer-logo-link">
                    <img src="<?= asset('images/home/mainlogo.svg') ?>" alt="<?= htmlspecialchars(env('SITENAME') ?: 'Store') ?>" class="footer-logo">
                </a>
                <p class="footer-tagline">Khám phá sản phẩm mới nhất và ưu đãi đặc biệt.</p>
            </div>
            <div class="footer-links">
                <h4 class="footer-heading">Liên kết nhanh</h4>
                <ul>
                    <li><a href="<?= $base ?>">Trang chủ</a></li>
                    <li><a href="<?= $base ?>/products">Sản phẩm</a></li>
                    <li><a href="<?= $base ?>/cart">Giỏ hàng</a></li>
                    <li><a href="<?= $base ?>/company">Giới thiệu</a></li>
                    <li><a href="<?= $base ?>/sale">Khuyến mãi</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4 class="footer-heading">Liên hệ</h4>
                <ul class="footer-contact-list">
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'hello@example.com') ?>"><?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'hello@example.com') ?></a></li>
                    <li><i class="fas fa-phone"></i> <?= htmlspecialchars(env('CONTACT_PHONE') ?: '0123-456-789') ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copyright">&copy; <?= date('Y') ?> <?= htmlspecialchars(env('SITENAME') ?: 'Store') ?>. Đã đăng ký bản quyền.</p>
        </div>
    </div>
</footer>
