<?php
$product = $product ?? null;
if (!$product) {
    echo '<p>Không tìm thấy sản phẩm.</p>';
    return;
}
$base = rtrim(base_url(), '/');
$images = $product->images ?? [];
$variants = $product->variants ?? [];
$mainImage = null;
foreach ($images as $img) {
    $row = is_array($img) ? $img : (array)$img;
    if (!empty($row['is_main'])) {
        $mainImage = $row['image_url'] ?? '';
        break;
    }
}
if (!$mainImage && !empty($images[0])) {
    $first = $images[0];
    $mainImage = is_array($first) ? ($first['image_url'] ?? '') : ($first->image_url ?? '');
}
if (!$mainImage) $mainImage = $product->thumbnail ?? '';
$mainImage = $mainImage ? (strpos($mainImage, 'http') === 0 || strpos($mainImage, '/') === 0 ? $mainImage : asset($mainImage)) : asset('images/home/placeholder.svg');
$price = isset($product->discount_price) && $product->discount_price > 0 ? (float)$product->discount_price : (float)($product->base_price ?? 0);
$originPrice = (float)($product->base_price ?? 0);
$hasDiscount = $originPrice > 0 && $price < $originPrice;
?>
<div class="product-detail-page">
    <div class="product-detail-container">
        <div class="product-detail-grid">
            <div class="product-gallery">
                <div class="product-main-image">
                    <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($product->name ?? '') ?>">
                </div>
                <?php if (count($images) > 1): ?>
                <div class="product-thumbnails">
                    <?php foreach ($images as $img): 
                        $url = is_array($img) ? ($img['image_url'] ?? '') : ($img->image_url ?? '');
                        $url = $url ? (strpos($url, 'http') === 0 || strpos($url, '/') === 0 ? $url : asset($url)) : $mainImage;
                    ?>
                    <button type="button" class="thumb-btn" data-src="<?= htmlspecialchars($url) ?>">
                        <img src="<?= htmlspecialchars($url) ?>" alt="">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="product-detail-info">
                <h1 class="product-detail-name"><?= htmlspecialchars($product->name ?? '') ?></h1>
                <div class="product-detail-price">
                    <?php if ($hasDiscount): ?>
                        <span class="price-old"><?= number_format($originPrice, 0, ',', '.') ?>đ</span>
                    <?php endif; ?>
                    <span class="price-current"><?= number_format($price, 0, ',', '.') ?>đ</span>
                </div>
                <?php if (!empty($product->description)): ?>
                <div class="product-detail-description">
                    <?= nl2br(htmlspecialchars($product->description)) ?>
                </div>
                <?php endif; ?>
                <?php if (empty($variants)): ?>
                    <p class="product-no-variant">Sản phẩm chưa có biến thể (màu/size). Không thể thêm vào giỏ. Vui lòng liên hệ cửa hàng.</p>
                <?php else: ?>
                <form method="post" action="<?= $base ?>/cart/add" class="product-add-cart-form" id="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?= (int)($product->id ?? 0) ?>">
                    <input type="hidden" name="product_variant_id" id="product_variant_id" value="">
                    <div class="product-options">
                        <p class="option-label">Chọn biến thể (màu / size) <span class="required">*</span></p>
                        <ul class="variant-list">
                            <?php foreach ($variants as $v): 
                                $v = is_array($v) ? (object)$v : $v;
                                $colorName = $v->color_name ?? 'Màu';
                                $sizeName = $v->size_name ?? '';
                                $label = trim($colorName . ($sizeName ? ' / ' . $sizeName : ''));
                                $vPrice = isset($v->price) ? (float)$v->price : $price;
                                $stock = $v->stock_quantity ?? null;
                                $vid = (int)($v->id ?? 0);
                            ?>
                            <li class="variant-item">
                                <button type="button" class="variant-btn" data-variant-id="<?= $vid ?>" data-price="<?= $vPrice ?>" data-sku="<?= htmlspecialchars($v->sku ?? '') ?>" <?= ($stock !== null && $stock < 1) ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($label) ?> — <?= number_format($vPrice, 0, ',', '.') ?>đ
                                    <?php if ($stock !== null && $stock < 1): ?> (Hết hàng)<?php endif; ?>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="variant-required-msg" id="variant-required-msg">Vui lòng chọn 1 biến thể bên trên.</p>
                    </div>
                    <div class="product-actions">
                        <label>Số lượng: <input type="number" name="quantity" class="product-qty" value="1" min="1" max="99"></label>
                        <button type="submit" class="btn-add-cart" id="btn-add-cart" disabled>Thêm vào giỏ</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var mainImg = document.querySelector('.product-main-image img');
    if (mainImg) {
        document.querySelectorAll('.product-thumbnails .thumb-btn').forEach(function(btn){
            btn.addEventListener('click', function(){ mainImg.src = this.dataset.src || ''; });
        });
    }
    var variantInput = document.getElementById('product_variant_id');
    var btnAddCart = document.getElementById('btn-add-cart');
    var msgRequired = document.getElementById('variant-required-msg');
    function updateAddCartState() {
        var selected = variantInput && variantInput.value !== '';
        if (btnAddCart) btnAddCart.disabled = !selected;
        if (msgRequired) msgRequired.style.visibility = selected ? 'hidden' : 'visible';
    }
    if (variantInput && btnAddCart) updateAddCartState();
    document.querySelectorAll('.variant-btn:not([disabled])').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.variant-btn').forEach(function(b){ b.classList.remove('selected'); });
            this.classList.add('selected');
            if (variantInput) variantInput.value = this.dataset.variantId || '';
            updateAddCartState();
        });
    });
    var form = document.getElementById('add-to-cart-form');
    if (form) form.addEventListener('submit', function(e){
        if (!variantInput || !variantInput.value) { e.preventDefault(); return false; }
    });
})();
</script>
