<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;

class OrderController extends BaseController
{
    const CART_KEY = 'cart';

    protected $orderRepository;
    protected $orderItemRepository;
    protected $userRepository;
    protected $categoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
        $this->userRepository = new UserRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    private function getCart(): array
    {
        return is_array($_SESSION[self::CART_KEY] ?? null) ? $_SESSION[self::CART_KEY] : [];
    }

    private function setCart(array $cart): void
    {
        $_SESSION[self::CART_KEY] = $cart;
    }

    /**
     * Checkout form page.
     */
    public function checkout()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            $this->flashMessage('Your cart is empty.', 'warning');
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        $total = 0;
        foreach ($cart as $item) {
            $total += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
        }
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $data = [
            'title' => 'Checkout',
            'description' => 'Complete your order',
            'categories' => $categories,
            'cart' => $cart,
            'cart_total' => $total,
        ];
        return $this->view('checkout/index', $data);
    }

    /**
     * Place order (POST).
     */
    public function place()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        $cart = $this->getCart();
        if (empty($cart)) {
            $this->flashMessage('Your cart is empty.', 'error');
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }

        $name = trim((string) ($_POST['customer_name'] ?? ''));
        $email = trim((string) ($_POST['customer_email'] ?? ''));
        $phone = trim((string) ($_POST['customer_phone'] ?? ''));
        $address = trim((string) ($_POST['customer_address'] ?? ''));
        $paymentMethod = trim((string) ($_POST['payment_method'] ?? 'cod'));

        $errors = [];
        if ($name === '') {
            $errors['customer_name'] = 'Name is required.';
        }
        if ($email === '') {
            $errors['customer_email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['customer_email'] = 'Invalid email.';
        }
        if ($phone === '') {
            $errors['customer_phone'] = 'Phone is required.';
        }
        if ($address === '') {
            $errors['customer_address'] = 'Address is required.';
        }

        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            $_SESSION['checkout_old'] = $_POST;
            return $this->redirect(rtrim(base_url(), '/') . '/checkout');
        }

        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
        }

        try {
            $user = $this->userRepository->findBy('email', $email);
            if ($user) {
                $userId = is_object($user) ? $user->id : $user['id'];
            } else {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'role' => 'guest',
                    'password' => password_hash('guest_' . uniqid('', true), PASSWORD_DEFAULT),
                ];
                $user = $this->userRepository->create($userData);
                if (!$user || !($user->id ?? null)) {
                    throw new \Exception('Could not create customer.');
                }
                $userId = is_object($user) ? $user->id : $user['id'];
            }

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'total_amount' => $cartTotal,
                'status' => 'pending',
                'payment_method' => $paymentMethod ?: 'cod',
            ]);
            if (!$order || !($order->id ?? null)) {
                throw new \Exception('Could not create order.');
            }
            $orderId = is_object($order) ? $order->id : $order['id'];

            $itemTable = (new \App\Models\OrderItem())->getTable();
            $db = (new \App\Models\OrderItem())->getDb();
            $orderItemRepo = new \App\Repositories\OrderItemRepository();
            $hasProductIdColumn = $orderItemRepo->orderItemsHaveProductIdColumn();

            foreach ($cart as $item) {
                $qty = (int)($item['quantity'] ?? 0);
                $price = (float)($item['price'] ?? 0);
                $productId = (int)($item['product_id'] ?? 0);
                $variantId = isset($item['product_variant_id']) && (int)$item['product_variant_id'] > 0
                    ? (int) $item['product_variant_id']
                    : null;
                $row = [
                    'order_id' => $orderId,
                    'product_variant_id' => $variantId,
                    'quantity' => $qty,
                    'price' => $price,
                ];
                if ($hasProductIdColumn && $productId > 0) {
                    $row['product_id'] = $productId;
                }
                $db->insert($itemTable, $row);
            }

            $this->setCart([]);
            unset($_SESSION['checkout_errors'], $_SESSION['checkout_old']);
            $this->flashMessage('Order placed successfully. Thank you!', 'success');
            return $this->redirect(rtrim(base_url(), '/') . '/order/success/' . $orderId);
        } catch (\Throwable $e) {
            $message = 'Could not place order. Please try again.';
            if (defined('APP_DEBUG') && APP_DEBUG || (function_exists('env') && env('APP_DEBUG'))) {
                $message .= ' [' . $e->getMessage() . ']';
            }
            $this->flashMessage($message, 'error');
            return $this->redirect(rtrim(base_url(), '/') . '/checkout');
        }
    }

    /**
     * Order success page.
     */
    public function success($id)
    {
        $id = (int) $id;
        $order = $this->orderRepository->findById($id);
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $data = [
            'title' => 'Order confirmed',
            'description' => 'Thank you for your order',
            'categories' => $categories,
            'order' => $order,
            'order_id' => $id,
        ];
        return $this->view('checkout/success', $data);
    }
}
