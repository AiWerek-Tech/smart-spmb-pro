<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(FCPATH);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
$app = \CodeIgniter\Boot::bootSpark($paths);

$url = 'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Effective URL: " . $effective_url . "\n";
if ($response === false) {
    echo "Curl error: " . $err . "\n";
} else {
    $data = json_decode($response, true);
    echo "Response preview: " . substr($response, 0, 200) . "\n";
    echo "Provinces count: " . (is_array($data) ? count($data) : 'not array') . "\n";
}
