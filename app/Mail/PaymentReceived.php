<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $invoice;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->invoice = $payment->invoice;
        $this->tenant = $payment->tenant;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $emailSettings = $this->tenant->settings['email'] ?? [];

        // Generate subject with placeholders
        $subject = $emailSettings['payment_subject'] ?? 'Konfirmasi Pembayaran - {doc_number}';
        $subject = str_replace(
            ['{company_name}', '{doc_number}', '{client_name}'],
            [$this->tenant->company_name, $this->invoice->invoice_number ?? '', $this->invoice->client->name ?? ''],
            $subject
        );

        $mail = $this->subject($subject)
            ->view('emails.payment-received')
            ->with([
                'payment' => $this->payment,
                'invoice' => $this->invoice,
                'tenant' => $this->tenant,
                'footer' => $emailSettings['footer'] ?? '',
            ]);

        // Add CC if configured
        if (!empty($emailSettings['cc'])) {
            $mail->cc($emailSettings['cc']);
        }

        // Add BCC if configured
        if (!empty($emailSettings['bcc'])) {
            $mail->bcc($emailSettings['bcc']);
        }

        return $mail;
    }
}
