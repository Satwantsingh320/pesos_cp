<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    protected $guarded = [];
    public $sortOrder = 'desc';
    public $sortEntity = 'categories.id';

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function scopeActive($query)
    {
        return $query->where('categories.status', 1);
    }
    public function pagination(Request $request)
    {
        $filter = 1;
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;

        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $filter .= " and (
                categories.name like '%" . addslashes($request->get('keyword')) . "%')";
        }

        if ($request->has('status') && $request->get('status') != '') {
            $filter .= " and categories.status = '" . addslashes($request->get('status')) . "'";
        }

        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query = $this->addSelect('categories.*')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }
    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('categories.id', $ids)->update(['status' => $status]);
        }
    }
    public function service($heading = true, $title = '-Select-')
    {
        $result = $this
            ->active()
            ->get(['id', 'name']);

        $service = [];
        // if ($heading) {
        //     $service[''] = $title;
        // }

        if (isset($result) && count($result) > 0) {
            foreach ($result as $row) {
                $service[$row->id] = $row->name;
            }
        }
        return $service;
    }

}
