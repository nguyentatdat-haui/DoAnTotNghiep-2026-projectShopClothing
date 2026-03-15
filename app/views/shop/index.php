<?php
$categoryName = $category_name ?? null;
$searchQuery = $search_query ?? '';
$isSalePage = !empty($is_sale_page);
$pag = $pagination ?? [];
$productList = $products ?? [];
$base = rtrim(base_url(), '/');
$pageTitle = $searchQuery !== '' ? 'Search: ' . htmlspecialchars($searchQuery) : ($categoryName ? htmlspecialchars($categoryName) : 'All Products');
?>
<div class="shop-page">
    <div class="shop-container">
        <h1 class="shop-title"><?= $pageTitle ?></h1>
        <?php if ($searchQuery !== ''): ?>
            <p class="shop-search-meta"><?= (int)($pag['total'] ?? 0) ?> result(s)</p>
        <?php endif; ?>
        <?php if (empty($productList)): ?>
            <p class="shop-empty"><?= $isSalePage ? 'No products on sale right now.' : ($searchQuery !== '' ? 'No products match your search.' : 'No products yet.') ?></p>
        <?php else: ?>
            <ul class="product-grid">
                <?php foreach ($productList as $p): 
                    $thumb = $p->thumbnail ?? '';
                    $imgSrc = $thumb ? (strpos($thumb, 'http') === 0 || strpos($thumb, '/') === 0 ? $thumb : asset($thumb)) : asset('images/home/placeholder.svg');
                    $price = isset($p->discount_price) && $p->discount_price > 0 ? (float)$p->discount_price : (float)($p->base_price ?? 0);
                    $originPrice = (float)($p->base_price ?? 0);
                    $hasDiscount = $originPrice > 0 && $price < $originPrice;
                    $link = $base . '/products/' . (int)($p->id ?? 0);
                ?>
                <li class="product-card">
                    <a href="<?= $link ?>" class="product-link">
                        <div class="product-image-wrap">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p->name ?? '') ?>" class="product-image" loading="lazy">
                            <?php if (!empty($p->is_new)): ?>
                                <span class="product-badge new">New</span>
                            <?php endif; ?>
                            <?php if (!empty($p->is_best_seller)): ?>
                                <span class="product-badge best">Best Seller</span>
                            <?php endif; ?>
                            <?php if ($hasDiscount): ?>
                                <span class="product-badge sale">Sale</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($p->name ?? '') ?></h3>
                            <div class="product-price">
                                <?php if ($hasDiscount): ?>
                                    <span class="price-old"><?= number_format($originPrice, 0, ',', '.') ?></span>
                                <?php endif; ?>
                                <span class="price-current"><?= number_format($price, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($pag['total_pages']) && $pag['total_pages'] > 1): 
                $listPath = $isSalePage ? ($base . '/sale') : (!empty($category_id) ? ($base . '/category/' . (int)$category_id) : ($base . '/products'));
                $querySuffix = $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '';
            ?>
            <nav class="shop-pagination" aria-label="Pagination">
                <ul class="pagination-list">
                    <?php if (!empty($pag['has_prev'])): ?>
                    <li><a href="<?= $listPath ?>?page=<?= $pag['current_page'] - 1 ?><?= $querySuffix ?>" class="pagination-link">&#8249; Previous</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pag['total_pages']; $i++): ?>
                    <li><a href="<?= $listPath ?>?page=<?= $i ?><?= $querySuffix ?>" class="pagination-link <?= $i === (int)$pag['current_page'] ? 'active' : '' ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if (!empty($pag['has_next'])): ?>
                    <li><a href="<?= $listPath ?>?page=<?= $pag['current_page'] + 1 ?><?= $querySuffix ?>" class="pagination-link">Next &#8250;</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
