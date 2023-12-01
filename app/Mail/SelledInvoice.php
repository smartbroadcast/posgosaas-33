<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SelledInvoice extends Mailable
{
    use Queueable, SerializesModels;
    public $sell;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sell)
    {
        $this->sell = $sell;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sell_send')->with('sell', $this->sell)->subject('Regarding to Products Selling');
    }
}
