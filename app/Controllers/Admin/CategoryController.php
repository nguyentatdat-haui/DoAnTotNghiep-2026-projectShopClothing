<?php

namespace App\Controllers\Admin;

use App\Repositories\CategoryRepository;

class CategoryController extends BaseAdminController
{
    protected $categoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = new CategoryRepository();
    }

    public function index()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $categories = $this->categoryRepository->findAll();

        return $this->view('admin/categories/index', [
            'title' => 'Quản lý danh mục',
            'current_page' => 'categories',
            'categories' => $categories
        ]);
    }

    public function create()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $allCategories = $this->categoryRepository->findAll();

        return $this->view('admin/categories/form', [
            'title' => 'Thêm danh mục',
            'current_page' => 'categories',
            'categories' => $allCategories
        ]);
    }

    public function store()
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $name = trim($_POST['name'] ?? '');
        $parentId = (int)($_POST['parent_id'] ?? 0);

        if ($name === '') {
            $this->error('Tên danh mục không được để trống.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/categories/create');
        }

        $this->categoryRepository->create([
            'name' => $name,
            'parent_id' => $parentId
        ]);

        $this->success('Đã thêm danh mục mới.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/categories');
    }

    public function edit($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $category = $this->categoryRepository->find($id);
        if (!$category) {
            $this->error('Không tìm thấy danh mục.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/categories');
        }

        $allCategories = $this->categoryRepository->findAll();

        return $this->view('admin/categories/form', [
            'title' => 'Sửa danh mục',
            'current_page' => 'categories',
            'category' => $category,
            'categories' => $allCategories
        ]);
    }

    public function update($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        $name = trim($_POST['name'] ?? '');
        $parentId = (int)($_POST['parent_id'] ?? 0);

        if ($id == $parentId) {
            $this->error('Danh mục cha không hợp lệ.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/categories/edit/' . $id);
        }

        if ($name === '') {
            $this->error('Tên danh mục không được để trống.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/categories/edit/' . $id);
        }

        $this->categoryRepository->update($id, [
            'name' => $name,
            'parent_id' => $parentId
        ]);

        $this->success('Đã cập nhật danh mục.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/categories');
    }

    public function delete($id)
    {
        if ($this->requireAdmin() !== null) {
            return $this->requireAdmin();
        }

        // Check if has children
        $children = $this->categoryRepository->getByParentId($id);
        if (!empty($children)) {
            $this->error('Không thể xóa danh mục có chứa danh mục con.');
            return $this->redirect(rtrim(base_url(), '/') . '/admin/categories');
        }

        $this->categoryRepository->delete($id);
        $this->success('Đã xóa danh mục.');
        return $this->redirect(rtrim(base_url(), '/') . '/admin/categories');
    }
}
