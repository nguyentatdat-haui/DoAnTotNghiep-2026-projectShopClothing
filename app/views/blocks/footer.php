<?php $base = rtrim(base_url(), '/'); ?>
<footer class="main-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= $base ?>" class="footer-logo-link">
                    <span class="footer-logo-text">CLOTHING SHOP</span>
                </a>
                <p class="footer-tagline">Tinh hoa thời trang hiện đại. Khám phá phong cách và định hình cá tính riêng của bạn cùng những bộ sưu tập đẳng cấp nhất.</p>
                
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-links">
                <h4 class="footer-heading">Cửa Hàng</h4>
                <ul>
                    <li><a href="<?= $base ?>/products">Hàng mới về</a></li>
                    <li><a href="<?= $base ?>/products">Sản phẩm bán chạy</a></li>
                    <li><a href="<?= $base ?>/sale">Bộ sưu tập Mùa hè</a></li>
                    <li><a href="<?= $base ?>/sale">Sản phẩm Sale</a></li>
                </ul>
            </div>
            
            <div class="footer-links">
                <h4 class="footer-heading">Trợ Giúp</h4>
                <ul>
                    <li><a href="<?= $base ?>/company">Về Chúng tôi</a></li>
                    <li><a href="<?= $base ?>/contact">Liên hệ</a></li>
                    <li><a href="<?= $base ?>/privacy">Chính sách bảo mật</a></li>
                    <li><a href="<?= $base ?>/terms">Điều khoản dịch vụ</a></li>
                </ul>
            </div>
            
            <div class="footer-contact">
                <h4 class="footer-heading">Nhận Tin Tức Mới Nhất</h4>
                <p style="color: #999; font-size: 14px; margin-bottom: 15px; line-height: 1.6;">Đăng ký để nhận thông báo về bộ sưu tập mới và ưu đãi độc quyền.</p>
                <form class="footer-newsletter-form">
                    <input type="email" placeholder="Nhập email của bạn..." required>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
                
                <ul class="footer-contact-list" style="margin-top: 30px;">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Đường Thời Trang, Quận Trung Tâm, TP. HCM</li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'cskh@clothingshop.vn') ?>"><?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'cskh@clothingshop.vn') ?></a></li>
                    <li><i class="fas fa-phone"></i> <a href="tel:<?= htmlspecialchars(env('CONTACT_PHONE') ?: '1900-123-456') ?>"><?= htmlspecialchars(env('CONTACT_PHONE') ?: '1900-123-456') ?></a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="footer-copyright">&copy; <?= date('Y') ?> <?= htmlspecialchars(env('SITENAME') ?: 'Clothing Shop') ?>. Đã đăng ký bản quyền. Designed with <i class="fas fa-heart" style="color: #d4af37;"></i>.</p>
            <div class="footer-payments">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fab fa-cc-amex"></i>
            </div>
        </div>
    </div>
</footer>
