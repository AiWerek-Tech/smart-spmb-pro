<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProvinceModel;
use App\Models\RegencyModel;
use App\Models\DistrictModel;
use App\Models\VillageModel;

class WilayahController extends BaseController
{
    protected ProvinceModel $provinceModel;
    protected RegencyModel $regencyModel;
    protected DistrictModel $districtModel;
    protected VillageModel $villageModel;

    public function __construct()
    {
        $this->provinceModel = new ProvinceModel();
        $this->regencyModel  = new RegencyModel();
        $this->districtModel = new DistrictModel();
        $this->villageModel  = new VillageModel();
    }

    private function fetchFromApi(string $url): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response === false) {
            return null;
        }
        return json_decode($response, true);
    }

    /**
     * Get all provinces.
     */
    public function provinces()
    {
        $data = $this->provinceModel->orderBy('name', 'ASC')->findAll();
        // If only sample data (<= 4) is present, fetch complete list
        if (count($data) <= 4) {
            $fetched = $this->fetchFromApi('http://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            if (is_array($fetched)) {
                foreach ($fetched as $item) {
                    if (!$this->provinceModel->find($item['id'])) {
                        $this->provinceModel->insert([
                            'id'   => $item['id'],
                            'name' => strtoupper($item['name'])
                        ]);
                    }
                }
                $data = $this->provinceModel->orderBy('name', 'ASC')->findAll();
            }
        }
        return $this->response->setJSON($data);
    }

    /**
     * Get regencies by province ID.
     */
    public function regencies()
    {
        $provinceId = $this->request->getGet('province_id');
        if (empty($provinceId)) {
            return $this->response->setJSON([]);
        }
        $data = $this->regencyModel->getByProvinceId($provinceId);
        if (empty($data)) {
            $fetched = $this->fetchFromApi("http://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            if (is_array($fetched)) {
                foreach ($fetched as $item) {
                    if (!$this->regencyModel->find($item['id'])) {
                        $this->regencyModel->insert([
                            'id'          => $item['id'],
                            'province_id' => $provinceId,
                            'name'        => strtoupper($item['name'])
                        ]);
                    }
                }
                $data = $this->regencyModel->getByProvinceId($provinceId);
            }
        }
        return $this->response->setJSON($data);
    }

    /**
     * Get districts by regency ID.
     */
    public function districts()
    {
        $regencyId = $this->request->getGet('regency_id');
        if (empty($regencyId)) {
            return $this->response->setJSON([]);
        }
        $data = $this->districtModel->getByRegencyId($regencyId);
        if (empty($data)) {
            $fetched = $this->fetchFromApi("http://www.emsifa.com/api-wilayah-indonesia/api/districts/{$regencyId}.json");
            if (is_array($fetched)) {
                foreach ($fetched as $item) {
                    if (!$this->districtModel->find($item['id'])) {
                        $this->districtModel->insert([
                            'id'         => $item['id'],
                            'regency_id' => $regencyId,
                            'name'       => strtoupper($item['name'])
                        ]);
                    }
                }
                $data = $this->districtModel->getByRegencyId($regencyId);
            }
        }
        return $this->response->setJSON($data);
    }

    /**
     * Get villages by district ID.
     */
    public function villages()
    {
        $districtId = $this->request->getGet('district_id');
        if (empty($districtId)) {
            return $this->response->setJSON([]);
        }
        $data = $this->villageModel->getByDistrictId($districtId);
        if (empty($data)) {
            $fetched = $this->fetchFromApi("http://www.emsifa.com/api-wilayah-indonesia/api/villages/{$districtId}.json");
            if (is_array($fetched)) {
                foreach ($fetched as $item) {
                    if (!$this->villageModel->find($item['id'])) {
                        $this->villageModel->insert([
                            'id'          => $item['id'],
                            'district_id' => $districtId,
                            'name'        => strtoupper($item['name'])
                        ]);
                    }
                }
                $data = $this->villageModel->getByDistrictId($districtId);
            }
        }
        return $this->response->setJSON($data);
    }
}

