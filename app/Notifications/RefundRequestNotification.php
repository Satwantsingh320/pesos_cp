<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RefundRequestNotification extends Notification
{
    use Queueable;

    protected $refund;

    public function __construct($refund)
    {
        $this->refund = $refund;
    }

    public function via($notifiable)
    {
        return ['database']; // stores in notifications table
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->refund->id,
            'order_number' => $this->refund->refund_number,
            'amount' => $this->refund->amount,
            'message' => 'New Refund Request #' . $this->refund->refund_number . ' submitted.',
            'url' => route('admin.refund.index'),
        ];
    }
}
