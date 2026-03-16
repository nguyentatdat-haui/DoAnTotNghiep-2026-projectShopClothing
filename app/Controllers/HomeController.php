<?php

namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
class HomeController extends BaseController
{
    protected $categoryRepository;
    protected $productRepository;

    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = new CategoryRepository();
        $this->productRepository = new ProductRepository();
    }

    public function index()
    {
        $pageTitle = 'Home';
        $pageDescription = 'Online store';
        $categories = $this->categoryRepository->getAllCategoriesWithChild();
        $featured = $newArrivals = $bestSellers = [];
        try {
            $featuredResult = $this->productRepository->getProductsForShop(1, 8, null);
            $featured = $featuredResult['data'] ?? [];
            $newArrivals = $this->productRepository->getLatest(8, 0);
            $bestSellers = $this->productRepository->getBestSellersFromOrders(8);
            if (empty($bestSellers)) {
                $bestSellers = $this->productRepository->getLatest(8, 8);
            }
        } catch (\Throwable $e) {
            // Products table may not exist yet
        }

        $data = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'categories' => $categories,
            'featured_products' => $featured,
            'new_arrivals' => $newArrivals,
            'best_sellers' => $bestSellers,
        ];

        return $this->view('home/index', $data);
    }

    public function breacum()
    {
        $data = [
            // 'title' => 'Breacum',
            // 'description' => 'Breacum page'
        ];

        return $this->view('home/breacum', $data);
    }

    public function maintenance()
    {
        $data = [
            'noindex' => true,
        ];
        return $this->view('maintenance/index', $data);
    }

    public function contact()
    {
        $data = [
            'styles' => ['contact.css'],
            'noindex' => true,
        ];
        return $this->view('home/contact', $data);
    }


    public function company()
    {
        $data = [
            'styles' => ['company.css'],
            'noindex' => true,
        ];
        return $this->view('home/company', $data);
    }


    public function privacy()
    {
        $data = [
            'styles' => ['privacy.css'],
            'noindex' => true,
        ];
        return $this->view('home/privacy', $data);
    }
}
