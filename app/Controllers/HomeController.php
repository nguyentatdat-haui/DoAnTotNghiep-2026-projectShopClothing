<?php

namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\BannerRepository;
use Cache;

class HomeController extends BaseController
{
    protected $categoryRepository;
    protected $productRepository;
    protected $locationData;

    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = new CategoryRepository();
        $this->productRepository = new ProductRepository();
        $this->locationData = null;
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
        $bannerMain = null;
        $bannerMid = null;
        $adSlots = [];
        try {
            $bannerRepo = new BannerRepository();
            $main = $bannerRepo->getBySlug('main');
            $mid = $bannerRepo->getBySlug('mid');
            if ($main) {
                $bannerMain = [
                    'image' => is_object($main) ? $main->image_url : $main['image_url'],
                    'link' => is_object($main) ? $main->link_url : $main['link_url'],
                    'alt' => is_object($main) ? $main->alt_text : $main['alt_text'],
                ];
            }
            if ($mid) {
                $bannerMid = [
                    'image' => is_object($mid) ? $mid->image_url : $mid['image_url'],
                    'link' => is_object($mid) ? $mid->link_url : $mid['link_url'],
                    'alt' => is_object($mid) ? $mid->alt_text : $mid['alt_text'],
                ];
            }
            foreach (['ad1', 'ad2', 'ad3'] as $slug) {
                $ad = $bannerRepo->getBySlug($slug);
                if ($ad && !empty(is_object($ad) ? $ad->image_url : $ad['image_url'])) {
                    $adSlots[] = [
                        'image' => is_object($ad) ? $ad->image_url : $ad['image_url'],
                        'link' => is_object($ad) ? $ad->link_url : $ad['link_url'],
                        'alt' => is_object($ad) ? $ad->alt_text : $ad['alt_text'],
                    ];
                }
            }
        } catch (\Throwable $e) {
            // site_banners table may not exist
        }
        $data = [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'categories' => $categories,
            'featured_products' => $featured,
            'new_arrivals' => $newArrivals,
            'best_sellers' => $bestSellers,
            'banner_main' => $bannerMain,
            'banner_mid' => $bannerMid,
            'ad_slots' => $adSlots,
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
