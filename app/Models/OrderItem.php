<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model
{

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function refundRequest()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function isRefundEligible()
    {
        // Must have delivered date
        if (!$this->order?->delivered_at) {
            return false;
        }

        // Already refunded?
        if ($this->is_refunded) {
            return false;
        }

        // Refund request already exists?
        if ($this->refundRequest && $this->refundRequest->status !== \App\Enums\RefundStatus::Rejected) {
            return false;
        }
        $returnDays = (int) ($this->product?->return_days ?? 0);
        if ($returnDays <= 0) {
            return false;
        }
        $expiryDate = $this->order->delivered_at->copy()->addDays($returnDays);
        return now()->lessThanOrEqualTo($expiryDate);
    }

    public function refundDaysLeft(): ?int
    {
        // Must have delivered date
        if (!$this->order?->delivered_at) {
            return null;
        }

        $returnDays = (int) ($this->product?->return_days ?? 0);

        if ($returnDays <= 0) {
            return null;
        }

        $expiryDate = $this->order->delivered_at->copy()->addDays($returnDays);

        // negative = expired
        return now()->diffInDays($expiryDate, false);
    }
}
