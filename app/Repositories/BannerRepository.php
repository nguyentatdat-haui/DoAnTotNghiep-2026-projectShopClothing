<?php

namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends BaseRepository
{
    protected $model = Banner::class;
    protected $table = 'site_banners';

    /**
     * Get banner by slug (e.g. main, mid, ad1, ad2, ad3).
     */
    public function getBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get all banners as array keyed by slug for admin form.
     */
    public function getAllBySlug()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY slug";
        $rows = $this->db->fetchAll($sql);
        $out = [];
        foreach ($rows ?: [] as $row) {
            $slug = $row['slug'] ?? '';
            $out[$slug] = $row;
        }
        return $out;
    }

    /**
     * Create or update a banner by slug.
     */
    public function saveBySlug($slug, $imageUrl, $linkUrl = '', $altText = '')
    {
        $existing = $this->getBySlug($slug);
        $data = [
            'image_url' => $imageUrl,
            'link_url' => $linkUrl,
            'alt_text' => $altText,
        ];
        if ($existing) {
            $id = is_object($existing) ? $existing->id : $existing['id'];
            $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
            return $this->findById($id);
        }
        $data['slug'] = $slug;
        $id = $this->db->insert($this->table, $data);
        return $id ? $this->findById($id) : null;
    }
}
