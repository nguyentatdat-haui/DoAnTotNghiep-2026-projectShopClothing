<?php
$cart = $cart ?? [];
$cartTotal = $cart_total ?? 0;
$base = rtrim(base_url(), '/');
$errors = $_SESSION['checkout_errors'] ?? [];
$old = $_SESSION['checkout_old'] ?? [];
?>
<div class="checkout-page">
    <div class="shop-container">
        <h1 class="shop-title">Checkout</h1>
        <?php if (!empty($errors)): ?>
            <ul class="checkout-errors">
                <?php foreach ($errors as $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="<?= $base ?>/checkout" class="checkout-form">
            <div class="checkout-grid">
                <div class="checkout-main">
                    <section class="checkout-block">
                        <h2>Contact & shipping</h2>
                        <div class="form-row">
                            <label for="customer_name">Name <span class="required">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($old['customer_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="customer_email">Email <span class="required">*</span></label>
                            <input type="email" id="customer_email" name="customer_email" value="<?= htmlspecialchars($old['customer_email'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="customer_phone">Phone <span class="required">*</span></label>
                            <input type="text" id="customer_phone" name="customer_phone" value="<?= htmlspecialchars($old['customer_phone'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="customer_address">Address <span class="required">*</span></label>
                            <textarea id="customer_address" name="customer_address" rows="3" required><?= htmlspecialchars($old['customer_address'] ?? '') ?></textarea>
                        </div>
                    </section>
                    <section class="checkout-block">
                        <h2>Payment</h2>
                        <div class="form-row">
                            <label><input type="radio" name="payment_method" value="cod" <?= ($old['payment_method'] ?? 'cod') === 'cod' ? 'checked' : '' ?>> Cash on delivery (COD)</label>
                        </div>
                        <div class="form-row">
                            <label><input type="radio" name="payment_method" value="bank" <?= ($old['payment_method'] ?? '') === 'bank' ? 'checked' : '' ?>> Bank transfer</label>
                        </div>
                    </section>
                </div>
                <div class="checkout-sidebar">
                    <section class="checkout-summary">
                        <h2>Order summary</h2>
                        <ul class="checkout-summary-list">
                            <?php foreach ($cart as $item):
                                $qty = (int)($item['quantity'] ?? 0);
                                $price = (float)($item['price'] ?? 0);
                                $sub = $qty * $price;
                            ?>
                            <li>
                                <span class="summary-name"><?= htmlspecialchars($item['name'] ?? '') ?> × <?= $qty ?></span>
                                <span class="summary-sub"><?= number_format($sub, 0, ',', '.') ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="checkout-total">Total: <strong><?= number_format($cartTotal, 0, ',', '.') ?></strong></div>
                        <button type="submit" class="btn-place-order">Place order</button>
                        <p class="checkout-back"><a href="<?= $base ?>/cart">← Back to cart</a></p>
                    </section>
                </div>
            </div>
        </form>
    </div>
</div>
