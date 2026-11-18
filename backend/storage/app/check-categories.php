<?php

use App\Models\Category;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "DB rows: ".Category::count().PHP_EOL;
foreach (Category::orderByDesc('id')->limit(15)->get(['id', 'parent_id', 'name', 'slug', 'is_active', 'created_at']) as $row) {
    echo $row->id.' | parent '.($row->parent_id ?? 'null').' | '.$row->name.' | active '.(int) $row->is_active.' | '.$row->created_at.PHP_EOL;
}

function call(string $method, string $url, ?string $body = null, ?string $token = null): array
{
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer '.$token;
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$code, json_decode((string) $res, true), (string) $res];
}

[$loginCode, $loginBody] = call('POST', 'http://127.0.0.1:8000/api/v1/auth/login', json_encode([
    'email' => 'admin@irack.com',
    'password' => 'Admin@iRACK2026!',
]));
$token = $loginBody['data']['token'] ?? '';
echo 'login '.$loginCode.PHP_EOL;

[$code, $body, $raw] = call('GET', 'http://127.0.0.1:8000/api/v1/admin/categories?tree=1', null, $token);
$data = $body['data'] ?? null;
echo 'tree '.$code.' data_type '.gettype($data).PHP_EOL;
if (is_array($data) && isset($data['data'])) {
    echo 'tree wrapped count '.count($data['data']).PHP_EOL;
    $roots = $data['data'];
} else {
    echo 'tree count '.(is_array($data) ? count($data) : 0).PHP_EOL;
    $roots = is_array($data) ? $data : [];
}
$first = $roots[0] ?? null;
if ($first) {
    $children = $first['children'] ?? null;
    echo 'first '.$first['name'].' children_type '.gettype($children);
    if (is_array($children)) {
        echo ' keys '.implode(',', array_slice(array_keys($children), 0, 6));
        echo ' count '.(isset($children['data']) ? count($children['data']) : count($children));
    }
    echo PHP_EOL;
}

$stamp = date('His');
[$createCode, $createBody, $createRaw] = call('POST', 'http://127.0.0.1:8000/api/v1/admin/categories', json_encode([
    'parent_id' => null,
    'name' => 'Check Category '.$stamp,
    'slug' => '',
    'description' => 'Temporary check',
    'sort_order' => 99,
    'is_active' => true,
]), $token);
echo 'create '.$createCode.' '.($createBody['message'] ?? substr($createRaw, 0, 300)).PHP_EOL;
$createdId = $createBody['data']['id'] ?? null;

if ($createdId && isset($roots[0]['id'])) {
    [$childCode, $childBody] = call('POST', 'http://127.0.0.1:8000/api/v1/admin/categories', json_encode([
        'parent_id' => $roots[0]['id'],
        'name' => 'Check Sub '.$stamp,
        'slug' => 'check-sub-'.$stamp,
        'description' => 'Temporary subcategory',
        'sort_order' => 1,
        'is_active' => true,
    ]), $token);
    echo 'child '.$childCode.' '.($childBody['message'] ?? '').' id '.($childBody['data']['id'] ?? 'none').PHP_EOL;
    $childId = $childBody['data']['id'] ?? null;
} else {
    $childId = null;
}

[$code2, $body2] = call('GET', 'http://127.0.0.1:8000/api/v1/admin/categories?tree=1', null, $token);
$data2 = $body2['data'] ?? [];
if (is_array($data2) && isset($data2['data'])) {
    $data2 = $data2['data'];
}
$foundRoot = false;
$foundChild = false;
$childVisibleAsArray = false;
foreach ($data2 as $root) {
    if (($root['name'] ?? '') === 'Check Category '.$stamp) {
        $foundRoot = true;
    }
    $children = $root['children'] ?? [];
    $list = isset($children['data']) ? $children['data'] : $children;
    if (!is_array($list)) {
        continue;
    }
    foreach ($list as $child) {
        if (($child['name'] ?? '') === 'Check Sub '.$stamp) {
            $foundChild = true;
            $childVisibleAsArray = !isset($children['data']);
        }
    }
}
echo 'shown_root '.($foundRoot ? 'yes' : 'no').' shown_child '.($foundChild ? 'yes' : 'no').' child_plain_array '.($childVisibleAsArray ? 'yes' : 'no').PHP_EOL;

if ($childId) {
    [$d1] = call('DELETE', 'http://127.0.0.1:8000/api/v1/admin/categories/'.$childId, null, $token);
    echo 'delete_child '.$d1.PHP_EOL;
}
if ($createdId) {
    [$d2] = call('DELETE', 'http://127.0.0.1:8000/api/v1/admin/categories/'.$createdId, null, $token);
    echo 'delete_root '.$d2.PHP_EOL;
}
