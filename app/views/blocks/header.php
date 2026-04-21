<?php
$headerCategories = $categories ?? [];
?>
<style>
    /* Perfectly Balanced Slim Header */
    .top-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid rgba(0,0,0,0.06) !important;
        box-shadow: 0 2px 15px rgba(0,0,0,0.03) !important;
        height: 90px !important; /* Slightly taller for a vertical logo */
    }
    .header-container {
        height: 90px !important;
        padding: 0 40px !important;
        max-width: 1500px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .header-logo {
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
    }
    .logo {
        height: 75px !important; /* Professional height */
        width: auto !important;
        mix-blend-mode: multiply !important;
        transition: transform 0.3s ease !important;
        display: block !important;
    }
    .logo:hover {
        transform: scale(1.05) !important;
    }
    .header-nav {
        flex: 1 !important;
        display: flex !important;
        justify-content: center !important;
    }
    .header-nav a {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #111 !important;
        letter-spacing: 0.5px !important;
        padding: 0 20px !important;
        height: 90px !important;
        line-height: 90px !important;
        text-transform: uppercase !important;
        transition: all 0.3s ease !important;
        display: inline-block !important;
    }
    .header-nav a:hover {
        color: #d4af37 !important;
    }
    .header-search-form {
        background: #f6f6f6 !important;
        border-radius: 50px !important;
        padding: 4px 8px !important;
        width: 260px !important;
        border: 1px solid transparent !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.3s ease !important;
    }
    .header-search-form:focus-within {
        background: #fff !important;
        border-color: #d4af37 !important;
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1) !important;
    }
    .header-search-input {
        background: transparent !important;
        border: none !important;
        font-size: 14px !important;
        padding: 8px 12px !important;
        outline: none !important;
        flex: 1 !important;
        color: #111 !important;
    }
    .header-search-btn {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        background: #111 !important;
        color: #fff !important;
        border: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
    }
    .header-icons {
        display: flex !important;
        align-items: center !important;
        gap: 20px !important;
    }
    .cart-icon {
        width: 48px !important;
        height: 48px !important;
        background: #f6f6f6 !important;
        border-radius: 50% !important;
        font-size: 18px !important;
        color: #111 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
        transition: all 0.3s ease !important;
    }
    .cart-icon:hover {
        background: #111 !important;
        color: #fff !important;
    }
    
    .header-icons {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
    }

    .user-account-link {
        width: 50px !important;
        height: 50px !important;
        background: #f6f6f6 !important;
        border-radius: 50% !important;
        font-size: 18px !important;
        color: #111 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
    }

    .user-account-link:hover {
        background: #111 !important;
        color: #fff !important;
    }

    /* User Profile Dropdown */
    .user-account-dropdown {
        position: relative;
    }
    .user-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: #fff;
        min-width: 220px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 15px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 1000;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .user-account-dropdown:hover .user-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(5px);
    }
    .user-info {
        padding: 0 20px 10px 20px;
        text-align: left !important;
    }
    .user-name {
        font-weight: 700;
        color: #111 !important;
        margin: 0 !important;
        font-size: 15px !important;
        text-transform: none !important;
        line-height: 1.2 !important;
    }
    .user-email {
        font-size: 12px !important;
        color: #666 !important;
        margin: 5px 0 0 0 !important;
        text-transform: none !important;
        line-height: 1.2 !important;
    }
    .user-dropdown-menu hr {
        border: 0;
        border-top: 1px solid #f0f0f0;
        margin: 10px 0;
    }
    .user-dropdown-menu a {
        display: flex !important;
        align-items: center !important;
        padding: 10px 20px !important;
        color: #333 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        height: auto !important;
        line-height: 1.4 !important;
        text-transform: none !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .user-dropdown-menu a i {
        margin-right: 12px !important;
        width: 16px !important;
        text-align: center !important;
        color: #d4af37 !important;
        font-size: 14px !important;
    }
    .user-dropdown-menu a:hover {
        background: #f9f9f9 !important;
        color: #d4af37 !important;
    }
    .user-dropdown-menu a.logout-link {
        color: #ff4d4d !important;
    }
    .user-dropdown-menu a.logout-link i {
        color: #ff4d4d !important;
    }
    .user-dropdown-menu a.logout-link:hover {
        background: #fff5f5 !important;
    }
</style>
<header class="top-header">
    <div class="header-top">
        <div class="header-container">
            <div class="header-logo">
                <a href="<?= base_url() ?>">
                    <img src="<?= asset('images/logo.png') ?>" alt="Clothing Shop" class="logo">
                </a>
            </div>
            <nav class="header-nav">
                <ul>
                    <li><a href="<?= base_url() ?>">TRANG CHỦ</a></li>
                    <li><a href="<?= base_url() ?>/company">GIỚI THIỆU</a></li>
                    <li class="dropdown">
                        <a href="<?= base_url() ?>/products">SẢN PHẨM <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <?php if (!empty($headerCategories)): ?>
                                <?php foreach ($headerCategories as $cat): ?>
                                    <div class="dropdown-column">
                                        <h4><?= htmlspecialchars($cat->name ?? $cat['name'] ?? '') ?></h4>
                                        <?php
                                        $children = $cat->children ?? $cat['children'] ?? [];
                                        if (!empty($children)):
                                        ?>
                                            <ul>
                                                <?php foreach ($children as $child): ?>
                                                    <?php $cid = is_object($child) ? $child->id : ($child['id'] ?? ''); ?>
                                                    <li><a href="<?= base_url() ?>/category/<?= (int)$cid ?>"><?= htmlspecialchars(is_object($child) ? $child->name : ($child['name'] ?? '')) ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li><a href="<?= base_url() ?>/sale">SẢN PHẨM SALE</a></li>
                </ul>
            </nav>
            <div class="header-icons">
                <div class="header-search-wrap">
                    <form action="<?= rtrim(base_url(), '/') ?>/products" method="get" class="header-search-form" role="search">
                        <input type="search" name="q" class="header-search-input" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" aria-label="Tìm kiếm sản phẩm">
                        <button type="submit" class="header-search-btn" title="Tìm kiếm"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <a href="<?= base_url() ?>/cart" class="icon-link cart-icon" title="Giỏ hàng" aria-label="Giỏ hàng (<?= (int)($cart_count ?? 0) ?> sản phẩm)">
                    <span class="cart-icon-wrap"><i class="fas fa-shopping-bag"></i></span>
                    <span class="cart-count"><?= (int)($cart_count ?? 0) ?></span>
                </a>

                <!-- User Account -->
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <div class="user-account-dropdown">
                        <a href="javascript:void(0)" class="user-account-link" title="Tài khoản">
                            <i class="fas fa-user"></i>
                        </a>
                        <div class="user-dropdown-menu">
                            <div class="user-info">
                                <p class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                                <p class="user-email"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
                            </div>
                            <hr>
                            <a href="<?= base_url() ?>/my-orders"><i class="fas fa-box"></i> Đơn hàng của tôi</a>
                            <a href="<?= base_url() ?>/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url() ?>/login" class="user-account-link" title="Đăng nhập">
                        <i class="fas fa-user"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
