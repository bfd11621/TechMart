<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope for top-level root categories.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Attach each category's own product count and on-hand stock, plus parent totals.
     */
    public static function attachInventoryTotals(Collection $categories, bool $publishedOnly = false): void
    {
        $query = DB::table('products')
            ->leftJoin('store_inventories', 'store_inventories.product_id', '=', 'products.id')
            ->selectRaw('products.category_id as category_id, COUNT(DISTINCT products.id) as product_count, COALESCE(SUM(store_inventories.quantity), 0) as stock_quantity')
            ->groupBy('products.category_id');

        if ($publishedOnly) {
            $query->where('products.is_published', true);
        }

        $stats = $query->get()->keyBy('category_id');

        $walk = function (Category $category) use (&$walk, $stats): void {
            $row = $stats->get($category->id);
            $ownProducts = (int) ($row->product_count ?? 0);
            $ownStock = (int) ($row->stock_quantity ?? 0);
            $category->setAttribute('products_count', $ownProducts);
            $category->setAttribute('stock_quantity', $ownStock);

            $childProducts = 0;
            $childStock = 0;
            $children = $category->relationLoaded('children') ? $category->children : collect();
            foreach ($children as $child) {
                $walk($child);
                $childProducts += (int) $child->total_products_count;
                $childStock += (int) $child->total_stock_quantity;
            }

            $category->setAttribute('total_products_count', $ownProducts + $childProducts);
            $category->setAttribute('total_stock_quantity', $ownStock + $childStock);
        };

        foreach ($categories as $category) {
            $walk($category);
        }
    }
}
