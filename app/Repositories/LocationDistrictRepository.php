<?php

namespace App\Repositories;

use App\Models\LocationDistrict;

class LocationDistrictRepository extends BaseRepository
{
    protected $model = LocationDistrict::class;

    public function getActive()
    {
        return $this->findWhere(['status' => 1]);
    }

    public function getByParentId($parentId)
    {
        return $this->findWhere(['parent_id' => $parentId, 'status' => 1]);
    }

    public function findActiveByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE (`name` = :name) AND `status` = 1 LIMIT 1";
        $row = $this->db->fetch($sql, ['name' => $name]);
        if ($row) {
            return $this->newModelInstance($row);
        }
        return null;
    }

    public function findActiveByCityAndDistrict($cityName, $districtName)
    {
        $sql = "SELECT 
                    d.id AS id,
                    d.parent_id,
                    c.id as city_id,
                    c.name as city_name,
                    d.name AS name,
                    d.japan_name,
                    d.iframe_url,
                    d.list_url,
                    d.status 
                FROM
                    location_city c
                    INNER JOIN location_district d ON d.parent_id = c.id 
                WHERE
                    (c.slug = :city_name OR c.name = :city_name2)
                    AND (d.name = :district_name)
                    AND c.status = 1
                    AND d.status = 1";

        $row = $this->db->fetch($sql, [
            'city_name' => $cityName,
            'city_name2' => $cityName,
            'district_name' => $districtName
        ]);

        if ($row) {
            return $this->newModelInstance($row);
        }
        return null;
    }
}
