<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::query()->where('email', 'admin@irack.com')->first();
$token = $user->createToken('inventory-check')->plainTextToken;
file_put_contents(storage_path('app/inventory-token.txt'), $token);

$stores = App\Models\Store::query()->get(['id', 'name', 'code']);
echo 'stores=' . $stores->count() . PHP_EOL;
foreach ($stores as $store) {
    echo $store->id . '|' . $store->code . '|' . $store->name . PHP_EOL;
}
echo 'inventory_rows=' . App\Models\StoreInventory::query()->count() . PHP_EOL;
echo 'products=' . App\Models\Product::query()->count() . PHP_EOL;
echo 'movements=' . App\Models\StockMovement::query()->count() . PHP_EOL;
echo 'qty=' . (int) App\Models\StoreInventory::query()->sum('quantity') . PHP_EOL;
