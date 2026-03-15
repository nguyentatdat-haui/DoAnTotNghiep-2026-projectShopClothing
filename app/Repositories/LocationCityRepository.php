<?php

namespace App\Repositories;

use App\Models\LocationCity;

class LocationCityRepository extends BaseRepository
{
    protected $model = LocationCity::class;

    public function getActive()
    {
        // Cache active cities for 10 minutes
        return \Cache::remember('location_city.active', 600, function () {
            return $this->findWhere(['status' => 1]);
        });
    }

    public function findActiveByName($name)
    {
        $cacheKey = 'location_city.by_name.' . md5($name);

        return \Cache::remember($cacheKey, 600, function () use ($name) {
            $sql = "SELECT * FROM {$this->table} WHERE (`slug` = :name OR `name` = :name2) AND `status` = 1 LIMIT 1";
            $row = $this->db->fetch($sql, ['name' => $name, 'name2' => $name]);
            if ($row) {
                return $this->newModelInstance($row);
            }
            return null;
        });
    }
}
