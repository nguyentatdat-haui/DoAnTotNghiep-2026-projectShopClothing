<?php

namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;

class CartController extends BaseController
{
    const SESSION_KEY = 'cart';

    protected $productRepository;
    protected $categoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Cart storage: $_SESSION['cart'][key] = [ product_id, product_variant_id, quantity, price, name, image, variant_label ]
     * key = product_id . '_' . (variant_id ?: 0)
     */
    private function getCart(): array
    {
        return is_array($_SESSION[self::SESSION_KEY] ?? null) ? $_SESSION[self::SESSION_KEY] : [];
    }

    private function setCart(array $cart): void
    {
        $_SESSION[self::SESSION_KEY] = $cart;
    }

    private function cartKey(int $productId, $variantId): string
    {
        return $productId . '_' . ((int) $variantId ?: 0);
    }

    public function index()
    {
        $cart = $this->getCart();
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $total = 0;
        foreach ($cart as $item) {
            $total += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
        }
        $data = [
            'title' => 'Cart',
            'description' => 'Shopping cart',
            'categories' => $categories,
            'cart' => $cart,
            'cart_total' => $total,
        ];
        return $this->view('cart/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $variantId = isset($_POST['product_variant_id']) ? (int) $_POST['product_variant_id'] : 0;
        if ($productId < 1 || $quantity < 1) {
            $this->flashMessage('Invalid product or quantity.', 'error');
            return $this->redirect($this->backUrl());
        }
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            $this->flashMessage('Product not found.', 'error');
            return $this->redirect($this->backUrl());
        }
        $productWithDetails = $this->productRepository->getByIdWithDetails($productId);
        $hasVariants = $productWithDetails && !empty($productWithDetails->variants);
        if (!$hasVariants) {
            $this->flashMessage('Sản phẩm này chưa có biến thể (màu/size), không thể thêm vào giỏ.', 'error');
            return $this->redirect($this->backUrl());
        }
        if ($variantId < 1) {
            $this->flashMessage('Vui lòng chọn biến thể (màu/size) trên trang sản phẩm.', 'error');
            return $this->redirect($this->backUrl());
        }
        $basePrice = (float) ($product->base_price ?? 0);
        $discountPrice = isset($product->discount_price) && $product->discount_price > 0 ? (float) $product->discount_price : null;
        $isOnSale = $discountPrice !== null && ($basePrice <= 0 || $discountPrice < $basePrice);
        $price = $isOnSale ? $discountPrice : $basePrice;
        $name = $product->name ?? '';
        $image = $product->thumbnail ?? '';
        $variantLabel = '';
        if ($variantId && $productWithDetails && !empty($productWithDetails->variants)) {
            foreach ($productWithDetails->variants as $v) {
                $v = is_array($v) ? (object) $v : $v;
                if ((int)($v->id ?? 0) === $variantId) {
                    if (!$isOnSale && isset($v->price)) {
                        $price = (float) $v->price;
                    }
                    $variantLabel = trim(($v->color_name ?? '') . ' / ' . ($v->size_name ?? ''));
                    break;
                }
            }
        }
        $key = $this->cartKey($productId, $variantId);
        $cart = $this->getCart();
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = (int)($cart[$key]['quantity'] ?? 0) + $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'product_variant_id' => $variantId ?: null,
                'quantity' => $quantity,
                'price' => $price,
                'name' => $name,
                'image' => $image,
                'variant_label' => $variantLabel,
            ];
        }
        $this->setCart($cart);
        $this->flashMessage('Added to cart.', 'success');
        return $this->redirect($this->backUrl());
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        $key = (string) ($_POST['key'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $cart = $this->getCart();
        if (!isset($cart[$key])) {
            $this->flashMessage('Item not found in cart.', 'error');
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        if ($quantity < 1) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $quantity;
        }
        $this->setCart($cart);
        $this->flashMessage('Cart updated.');
        return $this->redirect(rtrim(base_url(), '/') . '/cart');
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect(rtrim(base_url(), '/') . '/cart');
        }
        $key = (string) ($_POST['key'] ?? '');
        $cart = $this->getCart();
        unset($cart[$key]);
        $this->setCart($cart);
        $this->flashMessage('Item removed from cart.');
        return $this->redirect(rtrim(base_url(), '/') . '/cart');
    }

    private function backUrl(): string
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $base = rtrim(base_url(), '/');
        if ($ref && strpos($ref, $base) === 0) {
            return $ref;
        }
        return $base . '/products';
    }
}
