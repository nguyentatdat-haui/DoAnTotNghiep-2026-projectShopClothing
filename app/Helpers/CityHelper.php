<?php
namespace App\Helpers;


class CityHelper
{

    /**
     * Get appropriate Japanese suffix for city name
     * 
     * @param string $japan_name
     * @return string
     */
    private static function getJapaneseCitySuffix($japan_name)
    {
        switch ($japan_name) {
            case '北海道':
                return '';
            case '東京':
                return '都';
            case '大阪':
            case '京都':
                return '府';
            default:
                return '県';
        }
    }

    /**
     * Extract city name from URL
     * Example: http://mvc-base-tokyo.com/mvc-base -> tokyo
     * 
     * @return array ['city' => string, 'district' => string]
     * @throws Exception if URL parsing fails
     */
    public static function getCityNameFromUrl()
    {
        try {
            $baseUrl = base_url();
            $domainName = config('BASE_DOMAIN');

            if (empty($domainName)) {
                throw new \Exception('BASE_DOMAIN not configured');
            }

            // Parse URL to get host
            $parsedUrl = parse_url($baseUrl);
            $host = $parsedUrl['host'] ?? '';

            if (empty($host)) {
                throw new \Exception('Invalid URL format');
            }

            // Extract city and district from hostname
            $locationInfo = self::extractLocationFromHostname($host, $domainName);
            return $locationInfo;
        } catch (\Exception $e) {
            error_log("CityHelper::getCityNameFromUrl() failed: " . $e->getMessage());
            return [
                'city' => '',
                'district' => ''
            ];
        }
    }

    /**
     * Extract location information from hostname (domain only, no subdomain)
     *
     * @param string $host
     * @param string $domainName
     * @return array
     */
    private static function extractLocationFromHostname($host, $domainName)
    {
        // Remove subdomain - only use main domain
        // Example: www.tantei-navi-tokyo.com -> tantei-navi-tokyo.com
        $parts = explode('.', $host);
        $partsCount = count($parts);

        // Get main domain (last 2 parts: domain.com or domain.co.jp etc)
        if ($partsCount >= 2) {
            // Handle .co.jp, .com.au etc (3 part TLD)
            if (in_array($parts[$partsCount - 2], ['co', 'com', 'ne', 'or', 'ac', 'go'])) {
                // domain.co.jp format
                $mainDomain = $partsCount >= 3 ? $parts[$partsCount - 3] : $parts[0];
            } else {
                // domain.com format
                $mainDomain = $parts[$partsCount - 2];
            }
        } else {
            $mainDomain = $host;
        }

        // Remove base domain name to get city name
        // Example: tantei-navi-tokyo -> tokyo
        $cityName = str_replace($domainName . '-', '', $mainDomain);
        $cityName = str_replace('-' . $domainName, '', $cityName);

        $district = '';

        // Handle district-city format (e.g., shibuya-tokyo)
        if (strpos($cityName, '-') !== false) {
            $cityParts = explode('-', $cityName);
            if (count($cityParts) >= 2) {
                $district = $cityParts[0];  // district
                $cityName = $cityParts[1];  // city
            }
        }

        return [
            'city' => $cityName,
            'district' => $district
        ];
    }


    /**
     * Get city name with appropriate Japanese suffix
     * 
     * @param string $japan_name
     * @return string
     */
    public static function getCityNameWithSuffix($japan_name)
    {
        if (empty($japan_name)) {
            return '';
        }

        $suffix = self::getJapaneseCitySuffix($japan_name);
        return $japan_name . $suffix;
    }

    /**
     * Get city information by location data
     * 
     * @param array $locationinfo ['city' => string, 'district' => string]
     * @return array|null
     */
    public static function getCityInfoByName($locationinfo)
    {
        try {
            if (!is_array($locationinfo) || empty($locationinfo['city'])) {
                return null;
            }

            $repo = new \App\Repositories\LocationRepository();
            $city_name = $locationinfo['city'];
            $district_name = $locationinfo['district'] ?? '';

            $city = $repo->findActiveByName($city_name, $district_name);
            
            if (!$city) {
                return null;
            }

            $parent = $repo->findById($city->parent_id);

            $japan_name = self::formatJapaneseCityName($city);

            return [
                'city_id' => $city->id ?? null,
                'city_name' => $city->raw_name ?? null,
                'city_name_normal' => $city->name ?? null,
                'city_name_japan' => $japan_name,
                'parent_name' => $parent->name.'/' ?? null,
                'list_url' => $city->list_url ?? null,
            ];
        } catch (\Exception $e) {
            error_log("CityHelper::getCityInfoByName() failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format Japanese city name with appropriate suffix
     * 
     * @param object $city
     * @return string
     */
    private static function formatJapaneseCityName($city)
    {
        if (empty($city->japan_name)) {
            return '';
        }

        // Only add suffix for parent cities (prefectures)
        if ($city->parent_id == 0) {
            return self::getCityNameWithSuffix($city->japan_name);
        } else {
            return $city->japan_name;
        }
    }
}
