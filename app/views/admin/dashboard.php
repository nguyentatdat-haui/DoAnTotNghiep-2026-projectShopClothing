<?php
$adminBase = rtrim(base_url(), '/') . '/admin';
$totalProducts = $total_products ?? 0;
$totalOrders = $total_orders ?? 0;
$totalUsers = $total_users ?? 0;
$totalRevenue = $total_revenue ?? 0;
$recentOrders = $recent_orders ?? [];
?>

<div class="dashboard-stats">
    <!-- Revenue Card -->
    <div class="stat-card revenue">
        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-info">
            <span class="stat-label">Doanh thu</span>
            <h3 class="stat-value"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</h3>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="stat-card orders">
        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info">
            <span class="stat-label">Đơn hàng</span>
            <h3 class="stat-value"><?= number_format($totalOrders) ?></h3>
        </div>
    </div>

    <!-- Products Card -->
    <div class="stat-card products">
        <div class="stat-icon"><i class="fas fa-box"></i></div>
        <div class="stat-info">
            <span class="stat-label">Sản phẩm</span>
            <h3 class="stat-value"><?= number_format($totalProducts) ?></h3>
        </div>
    </div>
</div>

<div class="admin-grid" style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-top: 24px;">
    <div class="admin-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Đơn hàng gần đây</h2>
            <a href="<?= $adminBase ?>/orders" class="btn btn-sm btn-secondary">Xem tất cả</a>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $order): 
                            $statusClass = $order['status'];
                            $statusText = '';
                            switch($order['status']) {
                                case 'pending': $statusText = 'Chờ xử lý'; break;
                                case 'completed': $statusText = 'Hoàn thành'; break;
                                case 'cancelled': $statusText = 'Đã hủy'; break;
                                default: $statusText = $order['status'];
                            }
                        ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name'] ?? 'Khách vãng lai') ?></td>
                            <td style="font-weight: 600;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                            <td><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="<?= $adminBase ?>/orders/<?= $order['id'] ?>" class="btn btn-sm btn-primary">Chi tiết</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px;">Chưa có đơn hàng nào gần đây.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.05);
    }
    
    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .revenue .stat-icon { background: #ecfdf5; color: #10b981; }
    .orders .stat-icon { background: #eff6ff; color: #3b82f6; }
    .products .stat-icon { background: #fef3c7; color: #f59e0b; }
    
    .stat-label {
        display: block;
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 4px;
        font-weight: 500;
    }
    
    .stat-value {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }
    
    .table-responsive { width: 100%; overflow-x: auto; }
    
    table th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }
    
    table td {
        padding: 15px 12px;
        color: #334155;
        font-size: 14px;
    }
</style>
