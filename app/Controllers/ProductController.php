<?php

namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;

class ProductController extends BaseController
{
    protected $productRepository;
    protected $categoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Product listing page (with optional search by keyword).
     */
    public function index()
    {
        $page = (int) ($_GET['page'] ?? 1);
        $categoryId = !empty($_GET['category']) ? (int) $_GET['category'] : null;
        $searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        if ($searchQuery !== '') {
            $result = $this->productRepository->searchByKeyword($searchQuery, $page, 12);
        } else {
            $result = $this->productRepository->getProductsForShop($page, 12, $categoryId);
        }
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $data = [
            'title' => $searchQuery !== '' ? 'Search: ' . $searchQuery : 'Products',
            'description' => $searchQuery !== '' ? 'Search results for ' . $searchQuery : 'Product listing',
            'categories' => $categories,
            'products' => $result['data'],
            'pagination' => $result,
            'search_query' => $searchQuery,
        ];
        return $this->view('shop/index', $data);
    }

    /**
     * Product detail page.
     */
    public function show($id)
    {
        $id = (int) $id;
        $product = $this->productRepository->getByIdWithDetails($id);
        if (!$product) {
            http_response_code(404);
            $categories = $this->categoryRepository->getAllCategoriesWithChild();
            return $this->view('errors/404', ['title' => 'Not found', 'categories' => $categories]);
        }
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $data = [
            'title' => $product->name ?? 'Product detail',
            'description' => isset($product->description) ? mb_substr(strip_tags($product->description), 0, 160) : '',
            'categories' => $categories,
            'product' => $product,
        ];
        return $this->view('shop/show', $data);
    }

    /**
     * Products on sale (discount_price set and less than base_price).
     */
    public function sale()
    {
        $page = (int) ($_GET['page'] ?? 1);
        $result = $this->productRepository->getProductsOnSale($page, 12);
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $data = [
            'title' => 'Sale',
            'description' => 'Products on sale',
            'categories' => $categories,
            'products' => $result['data'],
            'pagination' => $result,
            'category_name' => 'Sale',
            'is_sale_page' => true,
        ];
        return $this->view('shop/index', $data);
    }

    /**
     * Products by category.
     */
    public function category($id)
    {
        $id = (int) $id;
        $page = (int) ($_GET['page'] ?? 1);
        $result = $this->productRepository->getProductsForShop($page, 12, $id);
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $category = $this->categoryRepository->findById($id);
        $categoryName = $category ? $category->name : 'Category';
        $data = [
            'title' => $categoryName,
            'description' => 'Products – ' . $categoryName,
            'categories' => $categories,
            'products' => $result['data'],
            'pagination' => $result,
            'category_id' => $id,
            'category_name' => $categoryName,
        ];
        return $this->view('shop/index', $data);
    }
}
