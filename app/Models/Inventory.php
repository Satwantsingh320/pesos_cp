<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'stock_type',
        'available_stock',
        'quantity',
        'updated_stock',
        'reference_type',
        'reference_id',
        'notes'
    ];

    public $sortEntity = 'inventories.id';
    public $sortOrder = 'desc';

    protected $casts = [
        'quantity' => 'integer',
        'available_stock' => 'integer',
        'updated_stock' => 'integer',
        'created_at' => 'datetime'
    ];

    /**
     * Get the product (for simple products)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the variant (for variant products)
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the product name (either from product or variant)
     */
    public function getProductNameAttribute()
    {
        if ($this->variant) {
            return $this->variant->product->name . ' (' . $this->variant->sku . ')';
        }
        return $this->product->name ?? '-';
    }

    /**
     * Get the SKU
     */
    public function getSkuAttribute()
    {
        if ($this->variant) {
            return $this->variant->sku;
        }
        return $this->product->sku_number ?? '-';
    }

    /**
     * Pagination for inventory
     */
    public function pagination(Request $request)
    {
        $filter = "1=1";
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;

        $query = Inventory::with(['product', 'variant.product']);

        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }

        // Search keyword
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $keyword = addslashes($request->get('keyword'));
            $filter .= " AND (
                inventories.stock_type LIKE '%{$keyword}%'
                OR products.name LIKE '%{$keyword}%'
                OR product_variants.sku LIKE '%{$keyword}%'
            )";
        }

        // Stock type filter
        if ($request->has('stock_type') && $request->get('stock_type') != '') {
            $filter .= " AND inventories.stock_type = '" . addslashes($request->get('stock_type')) . "'";
        }

        // Date filters
        if ($request->filled('start_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $filter .= " AND inventories.created_at >= '{$startDate}'";
        }

        if ($request->filled('end_date')) {
            $endDate = $request->end_date . ' 23:59:59';
            $filter .= " AND inventories.created_at <= '{$endDate}'";
        }

        // Sorting
        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query->select('inventories.*')
            ->leftJoin('products', 'products.id', '=', 'inventories.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'inventories.product_variant_id')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);

        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());

        return $query->paginate($perPage);
    }
}
