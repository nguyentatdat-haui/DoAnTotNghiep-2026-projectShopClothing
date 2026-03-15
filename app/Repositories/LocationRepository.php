<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository extends BaseRepository
{
    protected $model = Location::class;

    public function getActive()
    {
        return $this->findWhere(['status' => 1]);
    }

    public function getChildrenOf($parentId)
    {
        return $this->findWhere(['parent_id' => $parentId]);
    }

    public function findActiveByName($city, $district = null)
    {
        if ($city && $district) {
            $sql = "SELECT 
                        child.id AS id,
                        parent.id as parent_id,
                        parent.name as parent_name,
                        child.NAME AS name,
                        child.raw_name AS raw_name,
                        child.japan_name,
                        child.iframe_url,
                        child.list_url,
                        child.STATUS 
                    FROM
                        {$this->table} parent
                        INNER JOIN {$this->table} child ON child.parent_id = parent.id 
                    WHERE
                        (parent.raw_name = :city_name OR parent.name = :city_name)
                        AND (child.raw_name = :district_name OR child.name = :district_name)
                        AND parent.parent_id = 0 
                        AND parent.status = 1
                        AND child.status = 1";

            $row = $this->db->fetch($sql, [
                'city_name' => $city,
                'district_name' => $district
            ]);

            if ($row) {
                return $this->newModelInstance($row);
            }
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE (`raw_name` = :name OR `name` = :name2) AND `status` = 1 LIMIT 1";
            $row = $this->db->fetch($sql, ['name' => $city, 'name2' => $city]);
            if ($row) {
                return $this->newModelInstance($row);
            }
        }
        return null;
    }

    public function getDataLocationInfo($cityName)
    {
        $sql = "SELECT * FROM {$this->table} WHERE (`raw_name` = :city_name or `name` = :city_name2) AND `status` = 1 ";
        $row = $this->db->fetch($sql, ['city_name' => $cityName, 'city_name2' => $cityName]);
        if ($row) {
            return $this->newModelInstance($row);
        }
    }
}
