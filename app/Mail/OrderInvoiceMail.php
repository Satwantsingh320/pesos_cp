<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $isAdmin;

    public function __construct(Order $order, $isAdmin = false)
    {
        $this->order = $order;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        return $this->subject(
            $this->isAdmin
            ? 'New Order Received - ' . $this->order->order_number
            : 'Your Order Invoice - ' . $this->order->order_number
        )
            ->view('emails.order-invoice');
    }
}
