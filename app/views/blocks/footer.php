<?php $base = rtrim(base_url(), '/'); ?>
<footer class="main-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= $base ?>" class="footer-logo-link">
                    <img src="<?= asset('images/home/mainlogo.svg') ?>" alt="<?= htmlspecialchars(env('SITENAME') ?: 'Store') ?>" class="footer-logo">
                </a>
                <p class="footer-tagline">Discover the latest products and special offers.</p>
            </div>
            <div class="footer-links">
                <h4 class="footer-heading">Quick links</h4>
                <ul>
                    <li><a href="<?= $base ?>">Home</a></li>
                    <li><a href="<?= $base ?>/products">Products</a></li>
                    <li><a href="<?= $base ?>/cart">Cart</a></li>
                    <li><a href="<?= $base ?>/company">About</a></li>
                    <li><a href="<?= $base ?>/sale">Sale</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4 class="footer-heading">Contact</h4>
                <ul class="footer-contact-list">
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'hello@example.com') ?>"><?= htmlspecialchars(env('CONTACT_EMAIL') ?: 'hello@example.com') ?></a></li>
                    <li><i class="fas fa-phone"></i> <?= htmlspecialchars(env('CONTACT_PHONE') ?: '0123-456-789') ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copyright">&copy; <?= date('Y') ?> <?= htmlspecialchars(env('SITENAME') ?: 'Store') ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
