<div class="orders-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Đơn hàng của tôi</h1>
            <p class="page-subtitle">Xem lịch sử và tình trạng các đơn hàng bạn đã đặt</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-orders glass">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3>Bạn chưa có đơn hàng nào</h3>
                <p>Hãy bắt đầu mua sắm để nhận được những sản phẩm tuyệt vời nhất.</p>
                <a href="<?= base_url() ?>/products" class="btn btn-primary">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): 
                    $oid = is_object($order) ? $order->id : $order['id'];
                    $status = is_object($order) ? $order->status : $order['status'];
                    $total = is_object($order) ? $order->total_amount : $order['total_amount'];
                    $date = is_object($order) ? $order->created_at : $order['created_at'];
                    $items = is_object($order) ? $order->items : $order['items'];
                    
                    $statusClass = 'status-' . strtolower($status);
                    $statusText = [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ][$status] ?? $status;
                ?>
                    <div class="order-card glass">
                        <div class="order-header">
                            <div class="order-info-main">
                                <span class="order-id">Mã đơn hàng: #<?= $oid ?></span>
                                <span class="order-date"><i class="far fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($date)) ?></span>
                            </div>
                            <div class="order-status-wrap">
                                <span class="status-label">Trạng thái:</span>
                                <div class="order-status <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </div>
                            </div>
                        </div>
                        <div class="order-shipping-info">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><strong>Người nhận:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <span><strong>Số điện thoại:</strong> <?= htmlspecialchars($_SESSION['user_phone'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><strong>Địa chỉ:</strong> <?= htmlspecialchars($_SESSION['user_address'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <?php foreach ($items as $item): 
                                    $pName = is_object($item) ? $item->product_name : $item['product_name'];
                                    $pQty = is_object($item) ? $item->quantity : $item['quantity'];
                                    $pPrice = is_object($item) ? $item->price : $item['price'];
                                ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <p class="item-name"><?= htmlspecialchars($pName) ?></p>
                                            <p class="item-meta">Số lượng: <?= $pQty ?> x <?= number_format($pPrice, 0, ',', '.') ?>đ</p>
                                        </div>
                                        <div class="item-total">
                                            <?= number_format($pPrice * $pQty, 0, ',', '.') ?>đ
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="order-footer">
                            <div class="order-total">
                                <span>Tổng cộng:</span>
                                <span class="total-amount"><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                            <!-- <div class="order-actions">
                                <a href="#" class="btn btn-outline btn-sm">Xem chi tiết</a>
                            </div> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .orders-page {
        padding: 60px 0;
        background: #f8f9fa;
        min-height: 70vh;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .page-header {
        margin-bottom: 40px;
    }

    .page-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 32px;
        color: #111;
        margin-bottom: 10px;
    }

    .page-subtitle {
        color: #666;
        font-size: 16px;
    }

    .glass {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .empty-orders {
        text-align: center;
        padding: 80px 40px;
    }

    .empty-icon {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-orders h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #333;
    }

    .empty-orders p {
        color: #777;
        margin-bottom: 30px;
    }

    .order-card {
        margin-bottom: 25px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .order-header {
        padding: 20px 25px;
        background: #fcfcfc;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-status-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-label {
        font-size: 13px;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .order-shipping-info {
        padding: 20px 25px;
        background: rgba(212, 175, 55, 0.03);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 14px;
        color: #444;
    }

    .info-item i {
        color: #d4af37;
        font-size: 14px;
        margin-top: 3px;
    }

    .info-item strong {
        color: #111;
        margin-right: 5px;
    }

    .order-id {
        font-weight: 700;
        font-size: 16px;
        color: #111;
        display: block;
    }

    .order-date {
        font-size: 13px;
        color: #888;
        margin-top: 4px;
    }

    .order-date i {
        margin-right: 5px;
    }

    .order-status {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending { background: #fff8e1; color: #ff8f00; }
    .status-processing { background: #e3f2fd; color: #1976d2; }
    .status-shipping { background: #e8f5e9; color: #388e3c; }
    .status-completed { background: #e8f5e9; color: #388e3c; }
    .status-cancelled { background: #ffebee; color: #d32f2f; }

    .order-body {
        padding: 20px 25px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #eee;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .item-name {
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .item-meta {
        font-size: 13px;
        color: #777;
        margin: 4px 0 0 0;
    }

    .item-total {
        font-weight: 700;
        color: #111;
    }

    .order-footer {
        padding: 20px 25px;
        background: #fcfcfc;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .order-total {
        display: flex;
        align-items: center;
    }

    .order-total span {
        font-size: 14px;
        color: #666;
        font-weight: 600;
    }

    .total-amount {
        font-size: 24px !important;
        font-weight: 800 !important;
        color: #d32f2f !important;
        margin-left: 10px !important;
    }

    .btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        text-align: center;
        cursor: pointer;
    }

    .btn-primary {
        background: #111;
        color: #fff;
    }

    .btn-primary:hover {
        background: #333;
        transform: translateY(-2px);
    }

    .btn-outline {
        border: 1px solid #ddd;
        color: #333;
    }

    .btn-outline:hover {
        border-color: #111;
        background: #f9f9f9;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    @media (max-width: 576px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }
</style>
