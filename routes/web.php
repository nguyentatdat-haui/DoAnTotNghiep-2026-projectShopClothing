<?php

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Middleware\MiddlewareHelper;

// Home routes - có thể truyền số lần request tùy ý
$router->get('/', [HomeController::class, 'index'], [MiddlewareHelper::rateLimit()]); // 1 request/second
$router->get('/company', [HomeController::class, 'company']);

// Shop / Products
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{id}', [ProductController::class, 'show'])->where(['id' => '[0-9]+']);
$router->get('/sale', [ProductController::class, 'sale']);
$router->get('/category/{id}', [ProductController::class, 'category'])->where(['id' => '[0-9]+']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

// Checkout / Order
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/checkout', [OrderController::class, 'place']);
$router->get('/order/success/{id}', [OrderController::class, 'success'])->where(['id' => '[0-9]+']);

// maintenance routes
$router->get('/maintenance', [HomeController::class, 'maintenance']); // 10 requests/hour

// Chatbot routes
use App\Controllers\ChatBotController;
$router->get('/chatbot', [ChatBotController::class, 'index']);
$router->post('/chatbot/chat', [ChatBotController::class, 'chat']);


// ================================================================
// Admin (login không cần auth, các route khác redirect về /admin/login nếu chưa đăng nhập)
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\OrderController as AdminOrderController;
$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);

$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/create', [AdminProductController::class, 'create']);
$router->post('/admin/products/store', [AdminProductController::class, 'store']);
$router->get('/admin/products/edit/{id}', [AdminProductController::class, 'edit'])->where(['id' => '[0-9]+']);
$router->post('/admin/products/update/{id}', [AdminProductController::class, 'update'])->where(['id' => '[0-9]+']);
$router->post('/admin/products/delete/{id}', [AdminProductController::class, 'delete'])->where(['id' => '[0-9]+']);

$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show'])->where(['id' => '[0-9]+']);
$router->post('/admin/orders/update-status/{id}', [AdminOrderController::class, 'updateStatus'])->where(['id' => '[0-9]+']);

$router->get('/admin/categories', [\App\Controllers\Admin\CategoryController::class, 'index']);
$router->get('/admin/categories/create', [\App\Controllers\Admin\CategoryController::class, 'create']);
$router->post('/admin/categories/store', [\App\Controllers\Admin\CategoryController::class, 'store']);
$router->get('/admin/categories/edit/{id}', [\App\Controllers\Admin\CategoryController::class, 'edit'])->where(['id' => '[0-9]+']);
$router->post('/admin/categories/update/{id}', [\App\Controllers\Admin\CategoryController::class, 'update'])->where(['id' => '[0-9]+']);
$router->post('/admin/categories/delete/{id}', [\App\Controllers\Admin\CategoryController::class, 'delete'])->where(['id' => '[0-9]+']);

// Topic routes
// $router->group('/topic', function ($router) {
//     $router->get('/', [TopicController::class, 'index']);
//     $router->get('/{category}', [TopicController::class, 'Category'])
//         ->where('category', '[a-zA-Z\-]+');
//     $router->get('/{category}/{id}', [TopicController::class, 'Detail'])
//         ->where([
//             'category' => '[a-zA-Z\-]+',
//             'id' => '[0-9]+'
//         ]);
// }, [MiddlewareHelper::rateLimit()]);

// Location-based routes - must be placed after specific routes to avoid conflicts
// $router->get('/{city}', [HomeController::class, 'index'], [MiddlewareHelper::rateLimit(50, 3600)]); // city only route
// $router->get('/{city}/{district}', [HomeController::class, 'index'], [MiddlewareHelper::rateLimit(50, 3600)]); // city/district route


// ================================================================
// Lightweight cache clear endpoint (protect with token) ||| 
// Để xóa cache, vào file config.php, thêm CACHE_ADMIN_TOKEN = 'your_token_here'
// Vào đường dẫn xóa all http://localhost/your-app/_cache/flush?token=your_token_here 
// Để xóa cache theo prefix, vào đường dẫn http://localhost/your-app/_cache/flush?token=your_token_here&prefix=your_prefix_here
$router->get('/_cache/flush', function () {
    $token = $_GET['token'] ?? '';
    $expected = Config::get('CACHE_ADMIN_TOKEN', '');
    if (!$expected || !hash_equals((string)$expected, (string)$token)) {
        http_response_code(403);
        echo 'forbidden';
        return;
    }

    $prefix = $_GET['prefix'] ?? null;
    if ($prefix) {
        $deleted = Cache::deleteByPrefix($prefix);
        echo "deleted:$deleted entries with prefix '$prefix'";
    } else {
        Cache::flush();
        echo 'flushed';
    }
    exit();
});