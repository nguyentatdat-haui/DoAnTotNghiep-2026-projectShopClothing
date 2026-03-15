<?php
$headerCategories = $categories ?? [];
?>
<header class="top-header">
    <div class="header-top">
        <div class="header-container">
            <div class="header-logo">
                <a href="<?= base_url() ?>">
                    <img src="<?= asset('images/home/mainlogo.svg') ?>" alt="MARNI.Store" class="logo">
                </a>
            </div>
            <nav class="header-nav">
                <ul>
                    <li><a href="<?= base_url() ?>">HOME</a></li>
                    <li><a href="<?= base_url() ?>/company">ABOUT</a></li>
                    <li class="dropdown">
                        <a href="<?= base_url() ?>/products">PRODUCTS <i class="fas fa-chevron-down"></i></a>
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
                    <li><a href="<?= base_url() ?>/sale">SALE</a></li>
                </ul>
            </nav>
            <div class="header-icons">
                <div class="header-search-wrap">
                    <form action="<?= rtrim(base_url(), '/') ?>/products" method="get" class="header-search-form" role="search">
                        <input type="search" name="q" class="header-search-input" placeholder="Search products..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" aria-label="Search products">
                        <button type="submit" class="header-search-btn" title="Search"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <a href="<?= base_url() ?>/cart" class="icon-link cart-icon" title="Cart" aria-label="Cart (<?= (int)($cart_count ?? 0) ?> items)">
                    <span class="cart-icon-wrap"><i class="fas fa-shopping-bag"></i></span>
                    <span class="cart-count"><?= (int)($cart_count ?? 0) ?></span>
                </a>
            </div>
        </div>
    </div>
</header>
