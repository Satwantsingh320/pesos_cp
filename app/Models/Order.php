<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    protected $guarded = [];
    public $sortOrder = 'desc';
    public $sortEntity = 'orders.id';

    protected $casts = [
        'address' => 'array',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withTrashed();
        ;
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function pagination(Request $request)
    {
        $filter = 1;
        $perPage = 10;
        $sortOrder = $this->sortOrder;
        $sortEntity = $this->sortEntity;

        $query = Order::with('customer', 'product');
        if ($request->has('perPage') && $request->get('perPage') != '') {
            $perPage = $request->get('perPage');
        }
        if ($request->has('keyword') && $request->get('keyword') != '') {
            $filter .= " and (
                orders.order_number like '%" . addslashes($request->get('keyword')) . "%')";
        }

        if ($request->filled('status')) {
            $statuses = implode("','", (array) $request->status);
            $filter .= " AND orders.order_status IN ('$statuses')";
        }
        // Start Date
        if ($request->filled('start_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $filter .= " AND orders.created_at >= '{$startDate}'";
        }

        // End Date
        if ($request->filled('end_date')) {
            $endDate = $request->end_date . ' 23:59:59';
            $filter .= " AND orders.created_at <= '{$endDate}'";
        }


        if ($request->has('sortEntity') && $request->get('sortEntity') != '') {
            $sortEntity = $request->get('sortEntity');
        }

        if ($request->has('sortOrder') && $request->get('sortOrder') != '') {
            $sortOrder = $request->get('sortOrder');
        }

        $query = $this->addSelect('orders.*')
            ->leftJoin('customers', 'customers.id', 'orders.customer_id')
            ->whereRaw($filter)
            ->orderBy($sortEntity, $sortOrder);
        Cache::put(env('EXPORT_CACHE_KEY'), $request->all());
        $data = $query->paginate($perPage);
        return $data;
    }
}
