<?php

namespace App\Controllers\Admin;

use App\Repositories\BannerRepository;

class BannerController extends BaseAdminController
{
    protected $bannerRepository;

    private const UPLOAD_DIR = 'public/uploads/banners';
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_SIZE = 3 * 1024 * 1024; // 3MB

    public function __construct()
    {
        parent::__construct();
        $this->bannerRepository = new BannerRepository();
    }

    private function handleBannerUpload($slug)
    {
        $key = 'banner_image';
        if (empty($_FILES[$key]['name'][$slug]) || !is_uploaded_file($_FILES[$key]['tmp_name'][$slug] ?? '')) {
            return null;
        }
        $file = [
            'name' => $_FILES[$key]['name'][$slug],
            'type' => $_FILES[$key]['type'][$slug] ?? '',
            'tmp_name' => $_FILES[$key]['tmp_name'][$slug] ?? '',
            'size' => (int) ($_FILES[$key]['size'][$slug] ?? 0),
        ];
        if (!in_array($file['type'], self::ALLOWED_TYPES, true) || $file['size'] > self::MAX_SIZE) {
            return null;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $safeExt = strtolower(preg_replace('/[^a-z0-9]/', '', $ext)) ?: 'jpg';
        $filename = $slug . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
        $basePath = defined('APP_PATH') ? APP_PATH : (dirname(__DIR__, 2));
        $dir = $basePath . '/' . self::UPLOAD_DIR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return null;
        }
        return 'uploads/banners/' . $filename;
    }

    public function index()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $bySlug = [];
        try {
            $bySlug = $this->bannerRepository->getAllBySlug();
        } catch (\Throwable $e) {
            // Table may not exist yet
        }
        $slugs = ['main' => 'Banner chính', 'mid' => 'Banner giữa', 'ad1' => 'Quảng cáo 1', 'ad2' => 'Quảng cáo 2', 'ad3' => 'Quảng cáo 3'];
        $banners = [];
        foreach ($slugs as $slug => $label) {
            $banners[$slug] = [
                'label' => $label,
                'slug' => $slug,
                'image_url' => $bySlug[$slug]['image_url'] ?? '',
                'link_url' => $bySlug[$slug]['link_url'] ?? '',
                'alt_text' => $bySlug[$slug]['alt_text'] ?? '',
            ];
        }
        return $this->view('admin/banners/index', [
            'title' => 'Banner',
            'current_page' => 'banners',
            'banners' => $banners,
        ]);
    }

    public function save()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $slugs = ['main', 'mid', 'ad1', 'ad2', 'ad3'];
        foreach ($slugs as $slug) {
            $uploadedPath = $this->handleBannerUpload($slug);
            $imageUrl = $uploadedPath !== null
                ? $uploadedPath
                : trim($_POST['image_url'][$slug] ?? '');
            $linkUrl = trim($_POST['link_url'][$slug] ?? '');
            $altText = trim($_POST['alt_text'][$slug] ?? '');
            try {
                $existing = $this->bannerRepository->getBySlug($slug);
                if ($existing && $imageUrl === '') {
                    $imageUrl = is_object($existing) ? ($existing->image_url ?? '') : ($existing['image_url'] ?? '');
                }
                $this->bannerRepository->saveBySlug($slug, $imageUrl, $linkUrl, $altText);
            } catch (\Throwable $e) {
                // Table may not exist
            }
        }
        $this->success('Đã lưu cài đặt banner.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/banners');
    }
}
