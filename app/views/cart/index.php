<?php
$cart = $cart ?? [];
$cartTotal = $cart_total ?? 0;
$base = rtrim(base_url(), '/');
?>
<div class="cart-page">
    <div class="shop-container">
        <h1 class="shop-title">Giỏ hàng</h1>
        <?php if (empty($cart)): ?>
            <p class="shop-empty">Giỏ hàng của bạn đang trống.</p>
            <p><a href="<?= $base ?>/products" class="pagination-link">Tiếp tục mua sắm</a></p>
        <?php else: ?>
            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tạm tính</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $key => $item):
                            $qty = (int)($item['quantity'] ?? 0);
                            $price = (float)($item['price'] ?? 0);
                            $sub = $qty * $price;
                            $img = $item['image'] ?? '';
                            $imgSrc = $img ? (strpos($img, 'http') === 0 || strpos($img, '/') === 0 ? $img : asset($img)) : '';
                        ?>
                        <tr>
                            <td class="cart-product">
                                <?php if ($imgSrc): ?>
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" class="cart-product-image">
                                <?php endif; ?>
                                <div>
                                    <strong><?= htmlspecialchars($item['name'] ?? '') ?></strong>
                                    <?php if (!empty($item['variant_label'])): ?>
                                        <br><span class="cart-variant"><?= htmlspecialchars($item['variant_label']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="cart-price"><?= number_format($price, 0, ',', '.') ?></td>
                            <td class="cart-qty">
                                <form method="post" action="<?= $base ?>/cart/update" class="cart-update-form">
                                    <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                    <input type="number" name="quantity" value="<?= $qty ?>" min="1" max="99" class="cart-qty-input">
                                    <button type="submit" class="btn-cart-update">Cập nhật</button>
                                </form>
                            </td>
                            <td class="cart-subtotal"><?= number_format($sub, 0, ',', '.') ?></td>
                            <td class="cart-remove">
                                <form method="post" action="<?= $base ?>/cart/remove" onsubmit="return confirm('Xoá sản phẩm này khỏi giỏ hàng?');">
                                    <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                    <button type="submit" class="btn-cart-remove" title="Xoá">&#10005;</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="cart-footer">
                <div class="cart-total">Tổng tiền: <strong><?= number_format($cartTotal, 0, ',', '.') ?></strong></div>
                <p class="cart-actions">
                    <a href="<?= $base ?>/products" class="pagination-link">Tiếp tục mua sắm</a>
                    <a href="<?= $base ?>/checkout" class="btn-checkout">Tiến hành thanh toán</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
