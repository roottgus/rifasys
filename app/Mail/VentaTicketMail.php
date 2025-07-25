<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VentaTicketMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $cliente;
    public $logo_url;

    public function __construct($ticket, $cliente, $logo_url = null)
    {
        $this->ticket   = $ticket;
        $this->cliente  = $cliente;
        $this->logo_url = $logo_url;
    }

    public function build()
    {
        $logoUrl = $this->logo_url ?: asset('images/logo.png');

        $viewData = [
            'ticket'  => $this->ticket,
            'cliente' => $this->cliente,
            'logoCid' => $logoUrl,
        ];

        return $this->subject('¡Tu ticket ha sido registrado!')
                    ->view('emails.venta_ticket', $viewData)
                    ->text('emails.venta_ticket_plain', $viewData);
    }
}
