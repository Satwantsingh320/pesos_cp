<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Authenticatable
{
    use Billable;
    use Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guard = 'customer';
    protected $guarded = [];
    public $sortOrder = 'asc';
    public $sortEntity = 'customers.id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }
    public function scopeActive($query)
    {
        return $query->where('customers.status', 1);
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
                customers.name like '%" . addslashes($request->get('keyword')) . "%'
                or customers.email like '%" . addslashes($request->get('keyword')) . "%'
                or customers.phone like '%" . addslashes($request->get('keyword')) . "%')";
        }

        if ($request->has('status') && $request->get('status') != '') {
            $filter .= " and customers.status = '" . addslashes($request->get('status')) . "'";
        }

        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query = $this->addSelect('customers.*')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }
    public function toggleStatus($status, $ids = [])
    {
        if (isset($ids) && count($ids) > 0) {
            return $this->whereIn('customers.id', $ids)->update(['status' => $status]);
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

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}
