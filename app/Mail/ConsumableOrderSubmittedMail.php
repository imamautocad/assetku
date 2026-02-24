<?php

namespace App\Mail;

use App\Models\ConsumableOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsumableOrderSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(ConsumableOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $logoPath = public_path('img/logo-vasanta.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
             $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

         $pdf = Pdf::loadView('pdf.consumable-order', [
            'order' => $this->order,
            'logo' =>  $logoBase64
            ])
              ->setPaper('A4', 'portrait');

            return $this->subject('[Consumable Request] '.$this->order->no_req.' - '.$this->order->user->name)
                ->view('emails.consumable.submitted')
                ->with(['order' => $this->order])
                ->attachData($pdf->output(), 'Permintaan-' . $this->order->no_req . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
    }
}
