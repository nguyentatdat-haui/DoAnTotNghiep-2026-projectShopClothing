<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    protected $table = 'categories';
    protected $model = Category::class;

    public function getByParentId($parent_id)
    {
        return $this->findWhere(['parent_id' => $parent_id]);
    }

    public function getRootCategories()
    {
        return $this->findWhere(['parent_id' => 0]);
    }

    public function getAllCategoriesWithChild()
    {
        $sql = "SELECT * FROM {$this->table} WHERE parent_id IS NULL OR parent_id = 0 ORDER BY id ASC";
        $rows = $this->db->fetchAll($sql);
        $childSql = "SELECT * FROM {$this->table} WHERE parent_id = :parent_id ORDER BY name ASC";
        $categories = [];
        foreach ($rows as $row) {
            $category = is_array($row) ? (object) $row : $row;
            $childRows = $this->db->fetchAll($childSql, ['parent_id' => $category->id]);
            $childrens = array_map(function ($c) {
                return is_array($c) ? (object) $c : $c;
            }, $childRows);
            $category->children = $childrens;
            $category->childrens = $childrens;
            $categories[] = $category;
        }
        return $categories;
    }
}
