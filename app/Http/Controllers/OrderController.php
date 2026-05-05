<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Order())->sortEntity;
        $sortOrder = (new Order())->sortOrder;

        $result = null;
        $statusList = orderStatus();
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Order)->pagination($request);

            return view('admin.orders.pagination', compact('result', 'sortOrder', 'sortEntity', 'statusList'));
        }
        $url = url()->full();
        return view('admin.orders.index', compact('url', 'result', 'sortOrder', 'sortEntity', 'statusList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $inputs = $request->all();
        $rules = [
            'name' => 'required|string',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }

        $category = Category::create([
            'name' => $inputs['name'],
            'status' => $inputs['status'],
        ]);
        return redirect()->route('category.index')->with('success', __('admin.category_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {

        $order->load('customer', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function print(Order $order)
    {
        // Load relationships
        $order->load('customer', 'items.product');

        // Return a print-friendly view
        return view('admin.orders.print', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_status' => 'required|in:0,1,2,3,4,5',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->order_status = $request->order_status;
        if ($request->order_status == 3) {
            $order->delivered_at = now();
        }
        $order->save();

        // ✅ Status Labels (same as your UI logic)
        $statusLabels = [
            0 => 'Pendiente',
            1 => 'Pedido',
            2 => 'Enviado',
            3 => 'Entregado',
            4 => 'Cancelado',
            5 => 'Devuelto',
        ];
        /*     $statusLabels = [
                0 => 'Pending',
                1 => 'Ordered',
                2 => 'Shipped',
                3 => 'Delivered',
                4 => 'Cancelled',
                5 => 'Returned',
            ]; */

        $statusLabel = $statusLabels[$order->order_status] ?? 'Updated';

        // ✅ Send AFTER response (non-blocking without queue)
        if (!empty($order->customer->email)) {

            dispatch(function () use ($order, $statusLabel) {
                Mail::to($order->customer->email)
                    ->send(new OrderStatusUpdated($order, $statusLabel));
            })->afterResponse();
        }

        return response()->json([
            'success' => true,
            'message' => __('admin.order_status_updated_successfully'),
        ]);
    }

    public function updateTracking(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->update([
            'tracking_company' => $request->carrier,
            'tracking_number' => $request->tracking_number,
        ]);

        return response()->json(['success' => true, 'message' => __('admin.Tracking updated successfully!')]);
    }
}
