<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicDocumentController extends Controller
{
    public function verifyInvoice($code)
    {
        $invoice = Invoice::where('verification_code', $code)->firstOrFail();

        return view('public.verify-invoice', compact('invoice'));
    }

    public function downloadInvoicePdf($code)
    {
        $invoice = Invoice::where('verification_code', $code)->firstOrFail();
        $invoice->load(['client', 'items.product', 'tenant', 'creator']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function verifyQuotation($code)
    {
        $quotation = Quotation::where('verification_code', $code)->firstOrFail();

        return view('public.verify-quotation', compact('quotation'));
    }

    public function downloadQuotationPdf($code)
    {
        $quotation = Quotation::where('verification_code', $code)->firstOrFail();
        $quotation->load(['client', 'items.product', 'tenant', 'creator']);
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
        return $pdf->download("Quotation-{$quotation->quotation_number}.pdf");
    }

    public function verifyReceipt($code)
    {
        $receipt = \App\Models\Receipt::where('verification_code', $code)->firstOrFail();

        return view('public.verify-receipt', compact('receipt'));
    }

    public function downloadReceiptPdf($code)
    {
        $receipt = \App\Models\Receipt::where('verification_code', $code)->firstOrFail();
        $receipt->load(['invoice.tenant', 'invoice.client']);
        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));
        return $pdf->download("Kwitansi-{$receipt->receipt_number}.pdf");
    }

    public function verifyDeliveryNote($code)
    {
        $deliveryNote = \App\Models\DeliveryNote::where('verification_code', $code)->firstOrFail();

        return view('public.verify-delivery-note', compact('deliveryNote'));
    }

    public function downloadDeliveryNotePdf($code)
    {
        $deliveryNote = \App\Models\DeliveryNote::where('verification_code', $code)->firstOrFail();
        $deliveryNote->load(['invoice.tenant', 'items', 'creator']);
        $pdf = Pdf::loadView('pdf.delivery-note', compact('deliveryNote'));
        return $pdf->stream("SuratJalan-{$deliveryNote->delivery_number}.pdf"); // Stream usually better for validation check
    }
}
