<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceSent extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->tenant = $invoice->tenant;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $emailSettings = $this->tenant->settings['email'] ?? [];

        // Generate subject with placeholders
        $subject = $emailSettings['invoice_subject'] ?? 'Invoice dari {company_name} - {doc_number}';
        $subject = str_replace(
            ['{company_name}', '{doc_number}', '{client_name}'],
            [$this->tenant->company_name, $this->invoice->invoice_number, $this->invoice->client->name],
            $subject
        );

        // Generate PDF attachment
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this->invoice]);

        $mail = $this->subject($subject)
            ->view('emails.invoice-sent')
            ->with([
                'invoice' => $this->invoice,
                'tenant' => $this->tenant,
                'footer' => $emailSettings['footer'] ?? '',
            ])
            ->attachData(
                $pdf->output(),
                "Invoice-{$this->invoice->invoice_number}.pdf",
                ['mime' => 'application/pdf']
            );

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
