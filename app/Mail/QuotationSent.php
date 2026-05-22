<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationSent extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
        $this->tenant = $quotation->tenant;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $emailSettings = $this->tenant->settings['email'] ?? [];

        // Generate subject with placeholders
        $subject = $emailSettings['quotation_subject'] ?? 'Penawaran dari {company_name} - {doc_number}';
        $subject = str_replace(
            ['{company_name}', '{doc_number}', '{client_name}'],
            [$this->tenant->company_name, $this->quotation->quotation_number, $this->quotation->client->name],
            $subject
        );

        // Generate PDF attachment
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $this->quotation]);

        $mail = $this->subject($subject)
            ->view('emails.quotation-sent')
            ->with([
                'quotation' => $this->quotation,
                'tenant' => $this->tenant,
                'footer' => $emailSettings['footer'] ?? '',
            ])
            ->attachData(
                $pdf->output(),
                "Quotation-{$this->quotation->quotation_number}.pdf",
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
