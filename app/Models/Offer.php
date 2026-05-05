<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Offer extends Model
{
    protected $guarded = [];
    public $sortOrder = 'asc';
    public $sortEntity = 'offers.id';


    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
        ;
    }

    public function pagination(Request $request)
    {
        $filter = 1;
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;


        $query = Offer::with('product', 'product.category', 'product.subcategory'); // relation loaded
        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $filter .= " and (
                offers.title like '%" . addslashes($request->get('keyword')) . "%'
                or products.name like '%" . addslashes($request->get('keyword')) . "%'
                or sub_categories.name like '%" . addslashes($request->get('keyword')) . "%'
                or categories.name like '%" . addslashes($request->get('keyword')) . "%')";
        }


        if ($request->has('status') && $request->get('status') != '') {
            $filter .= " and offers.status = '" . addslashes($request->get('status')) . "'";
        }


        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query->addSelect('offers.*')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }

    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('offers.id', $ids)->update(['status' => $status]);
        }
    }


}
