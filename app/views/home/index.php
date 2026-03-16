<?php
$base = rtrim(base_url(), '/');
$featured = $featured_products ?? [];
$newArrivals = $new_arrivals ?? [];
$bestSellers = $best_sellers ?? [];
// Banner & ad slots (image có thể là path uploads/banners/xxx hoặc URL đầy đủ)
$banner_main = $banner_main ?? null;
$banner_mid  = $banner_mid ?? null;
$ad_slots    = $ad_slots ?? [];
function home_banner_img_src($url) {
    if (empty($url)) return '';
    if (strpos($url, 'http') === 0 || strpos($url, '//') === 0 || (strlen($url) > 0 && $url[0] === '/')) return $url;
    return asset($url);
}

function home_product_card($p, $base) {
    $isObj = is_object($p);
    $thumb = $isObj ? ($p->thumbnail ?? '') : ($p['thumbnail'] ?? '');
    $imgSrc = $thumb ? (strpos($thumb, 'http') === 0 || strpos($thumb, '/') === 0 ? $thumb : asset($thumb)) : '';
    $basePrice = (float)($isObj ? ($p->base_price ?? 0) : ($p['base_price'] ?? 0));
    $discountPrice = $isObj ? ($p->discount_price ?? null) : ($p['discount_price'] ?? null);
    $price = (isset($discountPrice) && $discountPrice !== '' && $discountPrice !== null && (float)$discountPrice > 0)
        ? (float)$discountPrice
        : $basePrice;
    $hasDiscount = $basePrice > 0 && $price > 0 && $price < $basePrice;
    $link = $base . '/products/' . (int)($isObj ? ($p->id ?? 0) : ($p['id'] ?? 0));
    $name = $isObj ? ($p->name ?? '') : ($p['name'] ?? '');
    ?>
    <li class="product-card">
        <a href="<?= $link ?>" class="product-link">
            <div class="product-image-wrap">
                <?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($name) ?>" class="product-image" loading="lazy"><?php else: ?><div class="product-image product-image--placeholder"></div><?php endif; ?>
                <?php if ($hasDiscount): ?><span class="product-badge product-badge--sale sale">Giảm giá</span><?php endif; ?>
            </div>
            <div class="product-info">
                <h3 class="product-name"><?= htmlspecialchars($name) ?></h3>
                <div class="product-price">
                    <?php if ($hasDiscount): ?>
                    <span class="price-old"><?= number_format($basePrice, 0, ',', '.') ?></span>
                    <?php endif; ?>
                    <span class="price-current"><?= number_format($price, 0, ',', '.') ?></span>
                </div>
            </div>
        </a>
    </li>
<?php
}
?>
<div class="home-page">
    <section class="hero-section">
        <div class="hero-content">
            <h1>Chào mừng bạn đến với cửa hàng</h1>
            <p>Khám phá sản phẩm mới nhất và ưu đãi đặc biệt.</p>
            <a href="<?= $base ?>/products" class="btn-hero">Xem sản phẩm</a>
        </div>
    </section>

    <!-- Main banner (full-width) -->
    <?php if (!empty($banner_main['image'])): 
        $mainImgSrc = home_banner_img_src($banner_main['image']);
        $mainLink = !empty($banner_main['link']) ? $banner_main['link'] : ($base . '/products');
    ?>
    <section class="home-banner home-banner--main" aria-label="Banner chính">
        <a href="<?= htmlspecialchars($mainLink) ?>" class="home-banner-link home-banner-link--main">
            <img src="<?= htmlspecialchars($mainImgSrc) ?>" alt="<?= htmlspecialchars($banner_main['alt'] ?? 'Banner') ?>" class="home-banner-img" loading="lazy">
        </a>
    </section>
    <?php endif; ?>

    <?php if (!empty($newArrivals)): ?>
    <section class="home-section home-section--alt">
        <div class="shop-container">
            <h2 class="home-section-title">Hàng mới về</h2>
            <p class="home-section-desc">Vừa lên kệ — lựa chọn mới dành cho bạn.</p>
            <ul class="product-grid product-grid--home">
                <?php foreach ($newArrivals as $p) { home_product_card($p, $base); } ?>
            </ul>
            <p class="home-section-link"><a href="<?= $base ?>/products" class="pagination-link">Xem tất cả sản phẩm mới</a></p>
        </div>
    </section>
    <?php endif; ?>

    <!-- Banner giữa trang -->
    <?php if (!empty($banner_mid['image'])): 
        $midImgSrc = home_banner_img_src($banner_mid['image']);
        $midLink = !empty($banner_mid['link']) ? $banner_mid['link'] : ($base . '/products');
    ?>
    <section class="home-banner home-banner--mid" aria-label="Banner giữa">
        <a href="<?= htmlspecialchars($midLink) ?>" class="home-banner-link home-banner-link--mid">
            <img src="<?= htmlspecialchars($midImgSrc) ?>" alt="<?= htmlspecialchars($banner_mid['alt'] ?? 'Banner') ?>" class="home-banner-img" loading="lazy">
        </a>
    </section>
    <?php endif; ?>

    <?php if (!empty($bestSellers)): ?>
    <section class="home-section">
        <div class="shop-container">
            <h2 class="home-section-title">Bán chạy</h2>
            <p class="home-section-desc">Được yêu thích nhất — nổi bật trong mùa này.</p>
            <ul class="product-grid product-grid--home">
                <?php foreach ($bestSellers as $p) { home_product_card($p, $base); } ?>
            </ul>
            <p class="home-section-link"><a href="<?= $base ?>/products" class="pagination-link">Xem tất cả sản phẩm bán chạy</a></p>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($featured)): ?>
    <section class="home-section home-section--alt">
        <div class="shop-container">
            <h2 class="home-section-title">Sản phẩm nổi bật</h2>
            <p class="home-section-desc">Gợi ý chọn lọc từ bộ sưu tập của chúng tôi.</p>
            <ul class="product-grid product-grid--home">
                <?php foreach (array_slice($featured, 0, 8) as $p) { home_product_card($p, $base); } ?>
            </ul>
            <p class="home-section-link"><a href="<?= $base ?>/products" class="pagination-link">Xem tất cả</a></p>
        </div>
    </section>
    <?php endif; ?>

    <!-- Khu vực quảng cáo (ad slots) -->
    <?php if (!empty($ad_slots)): ?>
    <section class="home-ads" aria-label="Quảng cáo">
        <div class="shop-container home-ads-inner">
            <?php foreach (array_slice($ad_slots, 0, 3) as $ad): 
                if (empty($ad['image'])) continue;
                $adImgSrc = home_banner_img_src($ad['image']);
                $adLink = !empty($ad['link']) ? $ad['link'] : '#';
            ?>
            <div class="home-ad-slot">
                <a href="<?= htmlspecialchars($adLink) ?>" class="home-ad-link">
                    <img src="<?= htmlspecialchars($adImgSrc) ?>" alt="<?= htmlspecialchars($ad['alt'] ?? '') ?>" class="home-ad-img" loading="lazy">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="home-cta">
        <div class="shop-container">
            <h2 class="home-cta-title">Sẵn sàng khám phá?</h2>
            <p class="home-cta-desc">Xem toàn bộ bộ sưu tập và tìm sản phẩm bạn yêu thích.</p>
            <a href="<?= $base ?>/products" class="btn-hero">Mua sắm tất cả sản phẩm</a>
        </div>
    </section>
</div>
<style>
.hero-section{text-align:center;padding:60px 20px;background:linear-gradient(135deg,#f8f6f0 0%,#eee 100%);}
.hero-content h1{margin:0 0 12px;font-size:2rem;}
.btn-hero{display:inline-block;padding:12px 28px;background:#1a1a1a;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;margin-top:16px;}
.btn-hero:hover{background:#d4af37;color:#1a1a1a;}
.home-section{padding:48px 20px;}
.home-section--alt{background:#fafafa;}
.home-section-title{margin:0 0 8px;font-size:1.75rem;}
.home-section-desc{margin:0 0 24px;color:#666;font-size:1rem;}
.home-section-link{text-align:center;margin-top:1.5rem;}
.product-grid--home{margin-bottom:0;}
.product-image--placeholder{background:#e0e0e0;}
.product-image-wrap{position:relative;}
.product-badge--sale{top:auto;left:auto;bottom:8px;right:8px;width:auto;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;border-radius:4px;background:#0d9488;color:#fff;box-shadow:0 1px 3px rgba(0,0,0,.15);}
.product-badge--sale::before{display:none;}
.product-info .product-price{margin-top:6px;}
.product-info .price-old{text-decoration:line-through;color:#888;font-size:0.9em;margin-right:8px;}
.product-info .price-current{font-weight:600;color:#1a1a1a;}
/* Banners */
.home-banner{width:100vw;max-width:100vw;margin-left:calc(-50vw + 50%);padding:0;overflow:hidden;box-sizing:border-box;}
.home-banner--main{}
.home-banner--mid{background:#f8f8f8;}
.home-banner-link{display:block;line-height:0;}
.home-banner-link:hover{opacity:.98;}
.home-banner-img{width:100%;height:auto;display:block;object-fit:cover;vertical-align:top;}
.home-banner-link--main .home-banner-img{max-height:min(420px,50vw);object-position:center;}
.home-banner-link--mid .home-banner-img{max-height:min(280px,35vw);object-position:center;}
/* Ad strip */
.home-ads{padding:32px 20px;background:#f5f5f5;}
.home-ads-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.home-ad-slot{border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.home-ad-link{display:block;line-height:0;}
.home-ad-link:hover{opacity:.95;}
.home-ad-img{width:100%;height:auto;display:block;object-fit:cover;min-height:100px;}
@media (max-width:768px){.home-ads-inner{grid-template-columns:1fr;gap:16px;}.home-ad-img{min-height:80px;}}
.home-cta{text-align:center;padding:56px 20px;background:linear-gradient(135deg,#1a1a1a 0%,#333 100%);color:#fff;}
.home-cta-title{margin:0 0 8px;font-size:1.5rem;color:#fff;}
.home-cta-desc{margin:0 0 20px;color:rgba(255,255,255,0.85);}
.home-cta .btn-hero{background:#fff;color:#1a1a1a;}
.home-cta .btn-hero:hover{background:#d4af37;color:#1a1a1a;}
</style>