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
    .logo-text {
        font-family: 'Outfit', sans-serif !important;
        font-size: 24px !important;
        font-weight: 800 !important;
        color: #d4af37 !important;
        letter-spacing: -0.5px !important;
        text-transform: uppercase !important;
        display: block !important;
        transition: transform 0.3s ease !important;
    }
    .logo-text:hover {
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
</style>
<header class="top-header">
    <div class="header-top">
        <div class="header-container">
            <div class="header-logo">
                <a href="<?= base_url() ?>" style="text-decoration: none !important;">
                    <span class="logo-text">CLOTHING SHOP</span>
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
            </div>
        </div>
    </div>
</header>
