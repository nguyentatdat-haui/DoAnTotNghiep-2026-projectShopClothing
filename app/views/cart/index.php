<?php
$cart = $cart ?? [];
$cartTotal = $cart_total ?? 0;
$base = rtrim(base_url(), '/');
?>
<div class="cart-page">
    <div class="shop-container">
        <h1 class="shop-title">Cart</h1>
        <?php if (empty($cart)): ?>
            <p class="shop-empty">Your cart is empty.</p>
            <p><a href="<?= $base ?>/products" class="pagination-link">Continue shopping</a></p>
        <?php else: ?>
            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
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
                                    <button type="submit" class="btn-cart-update">Update</button>
                                </form>
                            </td>
                            <td class="cart-subtotal"><?= number_format($sub, 0, ',', '.') ?></td>
                            <td class="cart-remove">
                                <form method="post" action="<?= $base ?>/cart/remove" onsubmit="return confirm('Remove this item?');">
                                    <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                                    <button type="submit" class="btn-cart-remove" title="Remove">&#10005;</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="cart-footer">
                <div class="cart-total">Total: <strong><?= number_format($cartTotal, 0, ',', '.') ?></strong></div>
                <p class="cart-actions">
                    <a href="<?= $base ?>/products" class="pagination-link">Continue shopping</a>
                    <a href="<?= $base ?>/checkout" class="btn-checkout">Proceed to checkout</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
