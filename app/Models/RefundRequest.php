<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    protected $fillable = [
        'refund_number',
        'order_id',
        'order_item_id',
        'customer_id',
        'amount',
        'status',
        'reason',
        'approved_at',
        'received_at',
        'refunded_at',
        'stripe_refund_id',
        'image',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => RefundStatus::class,
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeRequested(Builder $query): Builder
    {
        return $query->where('status', RefundStatus::Requested);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', RefundStatus::Approved);
    }

    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', RefundStatus::Refunded);
    }

    public function scopePendingAdmin(Builder $query): Builder
    {
        return $query->whereIn('status', [
            RefundStatus::Requested,
            RefundStatus::Approved,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Business State Helpers
    |--------------------------------------------------------------------------
    */

    public function canBeApproved(): bool
    {
        return $this->status === RefundStatus::Requested;
    }

    public function canBeRejected(): bool
    {
        return $this->status === RefundStatus::Requested;
    }

    public function canBeMarkedReceived(): bool
    {
        return $this->status === RefundStatus::Approved;
    }

    public function canBeRefunded(): bool
    {
        return $this->status === RefundStatus::ItemReceived;
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators / Actions
    |--------------------------------------------------------------------------
    */

    public function markApproved(): void
    {
        $this->update([
            'status' => RefundStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function markRejected(): void
    {
        $this->update([
            'status' => RefundStatus::Rejected,
        ]);
    }

    public function markItemReceived(): void
    {
        $this->update([
            'status' => RefundStatus::ItemReceived,
            'received_at' => now(),
        ]);
    }

    public function markRefunded(string $stripeRefundId): void
    {
        $this->update([
            'status' => RefundStatus::Refunded,
            'refunded_at' => now(),
            'stripe_refund_id' => $stripeRefundId,
        ]);

        // Prevent double refund
        $this->orderItem()->update([
            'is_refunded' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status->badge();
    }
}
