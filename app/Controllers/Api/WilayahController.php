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
        // If the database has fewer than 38 provinces, reset and fetch all including the new ones
        if (count($data) < 38) {
            $db = \Config\Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS=0;');
            $db->table('regions_villages')->truncate();
            $db->table('regions_districts')->truncate();
            $db->table('regions_regencies')->truncate();
            $db->table('regions_provinces')->truncate();
            $db->query('SET FOREIGN_KEY_CHECKS=1;');

            $fetched = $this->fetchFromApi('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
            if (is_array($fetched)) {
                // Official Kemendagri codes for Papua region (expanded from 2 to 6 provinces)
                $overrideProvinces = [
                    '91' => 'PAPUA',
                    '92' => 'PAPUA BARAT',
                    '93' => 'PAPUA SELATAN',
                    '94' => 'PAPUA TENGAH',
                    '95' => 'PAPUA PEGUNUNGAN',
                    '96' => 'PAPUA BARAT DAYA',
                ];

                foreach ($fetched as $item) {
                    $provId = $item['id'];
                    $provName = strtoupper($item['name']);

                    // Overwrite name if it belongs to Papua/Papua Barat to align with Kemendagri
                    if (isset($overrideProvinces[$provId])) {
                        $provName = $overrideProvinces[$provId];
                    }

                    $this->provinceModel->insert([
                        'id'   => $provId,
                        'name' => $provName
                    ]);
                }

                // Add missing new provinces (92, 93, 95, 96) which are not in the old emsifa provinces list
                foreach ($overrideProvinces as $provId => $provName) {
                    if (!$this->provinceModel->find($provId)) {
                        $this->provinceModel->insert([
                            'id'   => $provId,
                            'name' => $provName
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
            // Map the official Kemendagri province ID to the older emsifa API source province ID
            // Official 91 (PAPUA) -> emsifa 94 (PAPUA)
            // Official 92 (PAPUA BARAT) -> emsifa 91 (PAPUA BARAT)
            // Official 93 (PAPUA SELATAN) -> emsifa 94 (PAPUA)
            // Official 94 (PAPUA TENGAH) -> emsifa 94 (PAPUA)
            // Official 95 (PAPUA PEGUNUNGAN) -> emsifa 94 (PAPUA)
            // Official 96 (PAPUA BARAT DAYA) -> emsifa 91 (PAPUA BARAT)
            $sourceProvinceId = $provinceId;
            if (in_array($provinceId, ['91', '93', '94', '95'], true)) {
                $sourceProvinceId = '94';
            } elseif (in_array($provinceId, ['92', '96'], true)) {
                $sourceProvinceId = '91';
            }

            $fetched = $this->fetchFromApi("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$sourceProvinceId}.json");
            if (is_array($fetched)) {
                // Map old emsifa regency IDs to the correct 6 expanded Papua provinces
                $regencyToProvinceMap = [
                    // Papua Selatan (93)
                    '9401' => '93', // Merauke
                    '9413' => '93', // Boven Digoel
                    '9414' => '93', // Mappi
                    '9415' => '93', // Asmat

                    // Papua Tengah (94)
                    '9404' => '94', // Nabire
                    '9410' => '94', // Paniai
                    '9411' => '94', // Puncak Jaya
                    '9412' => '94', // Mimika
                    '9433' => '94', // Puncak
                    '9434' => '94', // Dogiyai
                    '9435' => '94', // Intan Jaya
                    '9436' => '94', // Deiyai

                    // Papua Pegunungan (95)
                    '9402' => '95', // Jayawijaya
                    '9416' => '95', // Yahukimo
                    '9417' => '95', // Pegunungan Bintang
                    '9418' => '95', // Tolikara
                    '9429' => '95', // Nduga
                    '9430' => '95', // Lanny Jaya
                    '9431' => '95', // Mamberamo Tengah
                    '9432' => '95', // Yalimo

                    // Papua Barat Daya (96)
                    '9106' => '96', // Sorong Selatan
                    '9107' => '96', // Sorong
                    '9108' => '96', // Raja Ampat
                    '9109' => '96', // Tambrauw
                    '9110' => '96', // Maybrat
                    '9171' => '96', // Kota Sorong

                    // Remaining Papua (91)
                    '9403' => '91', // Jayapura
                    '9408' => '91', // Kepulauan Yapen
                    '9409' => '91', // Biak Numfor
                    '9419' => '91', // Sarmi
                    '9420' => '91', // Keerom
                    '9426' => '91', // Waropen
                    '9427' => '91', // Supiori
                    '9428' => '91', // Mamberamo Raya
                    '9471' => '91', // Kota Jayapura

                    // Remaining Papua Barat (92)
                    '9101' => '92', // Fakfak
                    '9102' => '92', // Kaimana
                    '9103' => '92', // Teluk Wondama
                    '9104' => '92', // Teluk Bintuni
                    '9105' => '92', // Manokwari
                    '9111' => '92', // Manokwari Selatan
                    '9112' => '92', // Pegunungan Arfak
                ];

                foreach ($fetched as $item) {
                    $regId = $item['id'];
                    $targetProvinceId = $sourceProvinceId;

                    if (isset($regencyToProvinceMap[$regId])) {
                        $targetProvinceId = $regencyToProvinceMap[$regId];
                    }

                    // Only insert / cache if the calculated target province matches the currently requested province
                    if ($targetProvinceId === $provinceId) {
                        if (!$this->regencyModel->find($regId)) {
                            $this->regencyModel->insert([
                                'id'          => $regId,
                                'province_id' => $provinceId,
                                'name'        => strtoupper($item['name'])
                            ]);
                        }
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
            $fetched = $this->fetchFromApi("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$regencyId}.json");
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
            $fetched = $this->fetchFromApi("https://emsifa.github.io/api-wilayah-indonesia/api/villages/{$districtId}.json");
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

