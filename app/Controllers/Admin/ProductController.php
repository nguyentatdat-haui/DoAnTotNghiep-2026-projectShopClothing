<?php

namespace App\Controllers\Admin;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductImageRepository;
use App\Repositories\ColorRepository;
use App\Repositories\SizeRepository;
use App\Repositories\ProductVariantRepository;

class ProductController extends BaseAdminController
{
    protected $productRepository;
    protected $categoryRepository;
    protected $productImageRepository;
    protected $colorRepository;
    protected $sizeRepository;
    protected $productVariantRepository;

    /** Đường dẫn thư mục upload (trên đĩa). */
    private const UPLOAD_DIR = 'public/uploads/products';
    /** Loại ảnh cho phép. */
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_SIZE = 5 * 1024 * 1024; // 5MB

    public function __construct()
    {
        parent::__construct();
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->productImageRepository = new ProductImageRepository();
        $this->colorRepository = new ColorRepository();
        $this->sizeRepository = new SizeRepository();
        $this->productVariantRepository = new ProductVariantRepository();
    }

    /**
     * Upload file ảnh vào public/uploads/products. Trả về path lưu DB (uploads/products/xxx) hoặc null.
     */
    private function handleUpload($fileKey)
    {
        if (empty($_FILES[$fileKey]['tmp_name']) || !is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
            return null;
        }
        $file = $_FILES[$fileKey];
        $type = $file['type'] ?? '';
        $size = (int) ($file['size'] ?? 0);
        if (!in_array($type, self::ALLOWED_TYPES, true) || $size > self::MAX_SIZE) {
            return null;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $safeExt = strtolower(preg_replace('/[^a-z0-9]/', '', $ext)) ?: 'jpg';
        $filename = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
        $basePath = defined('APP_PATH') ? APP_PATH : (dirname(__DIR__, 2));
        $dir = $basePath . '/' . self::UPLOAD_DIR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return null;
        }
        return 'uploads/products/' . $filename;
    }

    /**
     * Upload nhiều ảnh (input name="extra_images[]"). Trả về mảng path.
     */
    private function handleMultipleUploads($fileKey)
    {
        $paths = [];
        if (empty($_FILES[$fileKey]['name']) || !is_array($_FILES[$fileKey]['name'])) {
            return $paths;
        }
        foreach ($_FILES[$fileKey]['name'] as $i => $name) {
            if (empty($name)) {
                continue;
            }
            $tmpName = $_FILES[$fileKey]['tmp_name'][$i] ?? '';
            $type = $_FILES[$fileKey]['type'][$i] ?? '';
            $size = (int) ($_FILES[$fileKey]['size'][$i] ?? 0);
            if (!is_uploaded_file($tmpName) || !in_array($type, self::ALLOWED_TYPES, true) || $size > self::MAX_SIZE) {
                continue;
            }
            $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg';
            $safeExt = strtolower(preg_replace('/[^a-z0-9]/', '', $ext)) ?: 'jpg';
            $filename = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
            $basePath = defined('APP_PATH') ? APP_PATH : (dirname(__DIR__, 2));
            $dir = $basePath . '/' . self::UPLOAD_DIR;
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $path = $dir . '/' . $filename;
            if (move_uploaded_file($tmpName, $path)) {
                $paths[] = 'uploads/products/' . $filename;
            }
        }
        return $paths;
    }

    public function index()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $result = $this->productRepository->paginate($page, 15);
        return $this->view('admin/products/index', [
            'title' => 'Quản lý sản phẩm',
            'current_page' => 'products',
            'products' => $result['data'],
            'pagination' => $result,
        ]);
    }

    public function create()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $categories = $this->categoryRepository->getAll(true, 500);
        $colors = $this->colorRepository->getAll(true, 200);
        $sizes = $this->sizeRepository->getAll(true, 200);
        return $this->view('admin/products/form', [
            'title' => 'Thêm sản phẩm',
            'current_page' => 'products',
            'product' => null,
            'categories' => $categories,
            'colors' => $colors,
            'sizes' => $sizes,
            'variants' => [],
        ]);
    }

    public function store()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $basePrice = (float) ($_POST['base_price'] ?? 0);
        $discountPrice = !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : null;
        $thumbnailUrl = trim($_POST['thumbnail'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        $isNew = isset($_POST['is_new']) ? 1 : 0;
        $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;

        if ($name === '') {
            $this->error('Tên sản phẩm không được để trống.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products/create');
        }

        $variantsFromPost = isset($_POST['variants']) && is_array($_POST['variants']) ? $_POST['variants'] : [];
        $validVariantCount = 0;
        foreach ($variantsFromPost as $row) {
            $colorId = isset($row['color_id']) ? (int) $row['color_id'] : 0;
            $sizeId = isset($row['size_id']) ? (int) $row['size_id'] : 0;
            if ($colorId > 0 || $sizeId > 0) {
                $validVariantCount++;
            }
        }
        if ($validVariantCount < 1) {
            $this->error('Bắt buộc phải thêm ít nhất một biến thể (màu/size) cho sản phẩm.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products/create');
        }

        $thumbPath = $this->handleUpload('thumbnail_file');
        if ($thumbPath) {
            $thumbnailUrl = $thumbPath;
        }

        $data = [
            'category_id' => $categoryId ?: null,
            'name' => $name,
            'description' => $description,
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'thumbnail' => $thumbnailUrl ?: null,
            'status' => $status,
            'is_new' => $isNew,
            'is_best_seller' => $isBestSeller,
        ];
        $product = $this->productRepository->create($data);
        if (!$product) {
            $this->error('Không thể thêm sản phẩm.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products/create');
        }
        $productId = is_object($product) ? $product->id : $product['id'];

        $mainPath = $thumbPath ?: $thumbnailUrl;
        if ($mainPath) {
            $this->productImageRepository->addImage($productId, $mainPath, 1);
        }
        $extraPaths = $this->handleMultipleUploads('extra_images');
        foreach ($extraPaths as $path) {
            $this->productImageRepository->addImage($productId, $path, 0);
        }

        $this->saveVariantsFromPost($productId, null);

        $this->success('Đã thêm sản phẩm.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
    }

    public function edit($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $product = $this->productRepository->getByIdWithDetails($id);
        if (!$product) {
            $this->error('Sản phẩm không tồn tại.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
        }
        $categories = $this->categoryRepository->getAll(true, 500);
        $colors = $this->colorRepository->getAll(true, 200);
        $sizes = $this->sizeRepository->getAll(true, 200);
        $variants = $this->productVariantRepository->getByProductId($id);
        return $this->view('admin/products/form', [
            'title' => 'Sửa sản phẩm',
            'current_page' => 'products',
            'product' => $product,
            'categories' => $categories,
            'colors' => $colors,
            'sizes' => $sizes,
            'variants' => $variants,
        ]);
    }

    public function update($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $product = $this->productRepository->findById($id);
        if (!$product) {
            $this->error('Sản phẩm không tồn tại.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $basePrice = (float) ($_POST['base_price'] ?? 0);
        $discountPrice = !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : null;
        $thumbnailUrl = trim($_POST['thumbnail'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        $isNew = isset($_POST['is_new']) ? 1 : 0;
        $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;

        if ($name === '') {
            $this->error('Tên sản phẩm không được để trống.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products/edit/' . $id);
        }

        $thumbPath = $this->handleUpload('thumbnail_file');
        if ($thumbPath) {
            $thumbnailUrl = $thumbPath;
            $this->productImageRepository->setAllNotMain($id);
            $this->productImageRepository->addImage($id, $thumbPath, 1);
        }

        $deleteIds = isset($_POST['delete_image_ids']) && is_array($_POST['delete_image_ids'])
            ? array_map('intval', array_filter($_POST['delete_image_ids'])) : [];
        foreach ($deleteIds as $imgId) {
            if ($imgId > 0) {
                $this->productImageRepository->delete($imgId);
            }
        }

        $extraPaths = $this->handleMultipleUploads('extra_images');
        foreach ($extraPaths as $path) {
            $this->productImageRepository->addImage($id, $path, 0);
        }

        $data = [
            'category_id' => $categoryId ?: null,
            'name' => $name,
            'description' => $description,
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'thumbnail' => $thumbnailUrl ?: null,
            'status' => $status,
            'is_new' => $isNew,
            'is_best_seller' => $isBestSeller,
        ];
        $deleteVariantIds = isset($_POST['delete_variant_ids']) && is_array($_POST['delete_variant_ids'])
            ? array_map('intval', array_filter($_POST['delete_variant_ids'])) : [];
        $variantsFromPost = isset($_POST['variants']) && is_array($_POST['variants']) ? $_POST['variants'] : [];
        $newValidCount = 0;
        foreach ($variantsFromPost as $row) {
            $colorId = isset($row['color_id']) ? (int) $row['color_id'] : 0;
            $sizeId = isset($row['size_id']) ? (int) $row['size_id'] : 0;
            if ($colorId > 0 || $sizeId > 0) {
                $newValidCount++;
            }
        }
        $currentVariants = $this->productVariantRepository->getByProductId($id);
        $deleteSet = array_flip($deleteVariantIds);
        $remainingAfterDelete = 0;
        foreach ($currentVariants as $v) {
            $vid = (int) (is_object($v) ? $v->id : ($v['id'] ?? 0));
            if ($vid > 0 && !isset($deleteSet[$vid])) {
                $remainingAfterDelete++;
            }
        }
        if ($remainingAfterDelete + $newValidCount < 1) {
            $this->error('Sản phẩm bắt buộc phải có ít nhất một biến thể (màu/size). Không thể xóa hết hoặc để trống.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products/edit/' . $id);
        }

        $this->productRepository->update($id, $data);

        foreach ($deleteVariantIds as $vid) {
            if ($vid > 0) {
                $this->productVariantRepository->delete($vid);
            }
        }
        $this->saveVariantsFromPost($id, $deleteVariantIds);

        $this->success('Đã cập nhật sản phẩm.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
    }

    private function saveVariantsFromPost($productId, $existingVariantIdsToSkip = null)
    {
        $product = $this->productRepository->findById($productId);
        $defaultPrice = 0;
        if ($product) {
            $base = (float) (is_object($product) ? $product->base_price : $product['base_price']) ?: 0;
            $discountVal = is_object($product) ? $product->discount_price : ($product['discount_price'] ?? null);
            $discount = ($discountVal !== null && $discountVal !== '') ? (float) $discountVal : 0;
            $defaultPrice = ($discount > 0) ? $discount : $base;
        }

        $variants = isset($_POST['variants']) && is_array($_POST['variants']) ? $_POST['variants'] : [];
        foreach ($variants as $row) {
            $colorId = isset($row['color_id']) ? (int) $row['color_id'] : 0;
            $sizeId = isset($row['size_id']) ? (int) $row['size_id'] : 0;
            if ($colorId === 0 && $sizeId === 0) {
                continue;
            }
            $priceRaw = $row['price'] ?? null;
            $price = ($priceRaw !== null && $priceRaw !== '') ? (float) $priceRaw : $defaultPrice;
            $stock = $row['stock_quantity'] ?? null;
            $sku = $row['sku'] ?? '';
            $this->productVariantRepository->addVariant($productId, $colorId ?: null, $sizeId ?: null, $price, $stock, $sku);
        }
    }

    public function delete($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }
        $product = $this->productRepository->findById($id);
        if (!$product) {
            $this->error('Sản phẩm không tồn tại.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
        }
        $this->productRepository->delete($id);
        $this->success('Đã xóa sản phẩm.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/products');
    }
}
