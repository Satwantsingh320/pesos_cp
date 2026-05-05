<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\RefundRequest;
use App\Models\User;
use App\Enums\RefundStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\StripeClient;
use App\Notifications\RefundRequestNotification;

class RefundRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CUSTOMER SIDE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|string|max:1000',
            'image' => ['nullable', 'image', 'max:5120'], // 5MB
        ]);

        $item = OrderItem::with(['order', 'product', 'refundRequest'])
            ->whereHas('order', fn($q) => $q->where('customer_id', auth()->id()))
            ->findOrFail($request->order_item_id);

        if (!$item->isRefundEligible()) {
            return back()->with('error', __('admin.Refund window expired or not allowed.'));
        }
        $Img = null;
        $path = public_path(REFUND_PATH);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);
            $Img = $fileName;
        }
        $refund = RefundRequest::create([
            'refund_number' => 'RF-' . strtoupper(Str::random(8)),
            'order_id' => $item->orders_id ?? $item->order_id,
            'order_item_id' => $item->id,
            'customer_id' => auth()->id(),
            'amount' => $item->price * $item->quantity,
            'status' => RefundStatus::Requested,
            'reason' => $request->reason,
            'image' => $Img ?? '',
        ]);
        $admins = User::all();
        foreach ($admins as $admin) {
            $admin->notify(new RefundRequestNotification($refund));
        }

        return redirect()->route('customer.refund.index')
            ->with('success', __('admin.Refund request submitted successfully.'));
    }

    public function customerIndex()
    {
        $refunds = RefundRequest::with(['order', 'orderItem'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('website.refunds', compact('refunds'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN SIDE
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request)
    {

        $query = RefundRequest::with(['order', 'orderItem', 'customer']);
        if ($request->keyword) {
            $query->where('refund_number', 'like', '%' . $request->keyword . '%')
                ->orWhereHas(
                    'order',
                    fn($q) =>
                    $q->where('order_number', 'like', '%' . $request->keyword . '%')
                );
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->perPage ?? 10;

        $result = $query->latest()->paginate($perPage);

        return view('admin.refunds.index', compact('result'));
    }

    public function approve(RefundRequest $refund)
    {
        if (!$refund->canBeApproved()) {
            abort(403);
        }

        $refund->markApproved();

        return back()->with('success', __('admin.Refund approved.'));
    }

    public function reject(RefundRequest $refund)
    {
        if (!$refund->canBeRejected()) {
            abort(403);
        }

        $refund->markRejected();

        return back()->with('success', __('admin.Refund rejected.'));
    }

    public function markReceived(RefundRequest $refund)
    {
        if (!$refund->canBeMarkedReceived()) {
            abort(403);
        }

        $refund->markItemReceived();

        return back()->with('success', __('admin.Item marked as received.'));
    }

    public function processRefund(RefundRequest $refund)
    {
        if (!$refund->canBeRefunded()) {
            abort(403);
        }

        DB::transaction(function () use ($refund) {

            $stripe = new StripeClient(config('services.stripe.STRIPE_SECRET'));

            $stripeRefund = $stripe->refunds->create([
                'payment_intent' => $refund->order->payment_intent_id,
                'amount' => $refund->amount * 100, // cents
            ]);

            $refund->markRefunded($stripeRefund->id);
        });

        return back()->with('success', __('admin.Refund processed successfully.'));
    }
}
