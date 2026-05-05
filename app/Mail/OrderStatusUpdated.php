<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use SerializesModels;

    public $order;
    public $statusLabel;

    public function __construct($order, $statusLabel)
    {
        $this->order = $order;
        $this->statusLabel = $statusLabel;
    }

    public function build()
    {
        return $this->subject('Order Status Updated - #' . $this->order->order_number)
            ->view('emails.order_status_updated');
    }
}
