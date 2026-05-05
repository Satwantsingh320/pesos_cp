<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubCategory extends Model
{
    protected $guarded = [];
    public $sortOrder = 'desc';
    public $sortEntity = 'sub_categories.id';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function scopeActive($query)
    {
        return $query->where('sub_categories.status', 1);
    }

    public function pagination(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        $allowedSorts = ['sub_categories.id', 'sub_categories.name', 'sub_categories.status'];

        $sortEntity = in_array($request->get('sortEntity'), $allowedSorts)
            ? $request->get('sortEntity')
            : 'sub_categories.id';

        $sortOrder = $request->input('sortOrder') ?? 'desc';

        $query = self::with('category')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('sub_categories.*');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('sub_categories.name', 'like', "%{$keyword}%")
                    ->orWhere('categories.name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('sub_categories.status', $request->status);
        }

        $query->orderBy($sortEntity, $sortOrder);

        Cache::put(
            'export.sub_categories.' . auth()->id(),
            $request->all(),
            now()->addMinutes(10)
        );

        return $query->paginate($perPage);
    }

    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('sub_categories.id', $ids)->update(['status' => $status]);
        }
    }

    public function service($heading = true, $title = '-Select-', $search = [])
    {
        $filter = 1;
        if (isset($search) && count($search) > 0) {
            $f1 = (isset($search['category_id']) && $search['category_id'] != '') ?
                ' and sub_categories.category_id = "' . addslashes($search['category_id']) . '"' : '';
            $filter .= $f1;
        }

        $result = $this
            ->whereRaw($filter)
            ->active()
            ->get(['id', 'name']);

        $service = [];
        if ($heading) {
            $service[''] = $title;
        }

        if (isset($result) && count($result) > 0) {
            foreach ($result as $row) {
                $service[$row->id] = $row->name;
            }
        }
        return $service;
    }


}
