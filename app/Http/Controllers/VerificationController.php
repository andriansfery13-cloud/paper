<?php

namespace App\Http\Controllers;

use App\Models\DocumentVerification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show document verification page (public)
     */
    public function show($code)
    {
        $verification = DocumentVerification::where('verification_code', $code)->first();

        if (!$verification) {
            return view('verification.not-found');
        }

        // Record the view
        $verification->recordView();

        // Get the document
        $document = $verification->getDocument();

        if (!$document) {
            return view('verification.not-found');
        }

        // Verify integrity
        $isValid = $verification->isValid();

        // Get tenant info
        $tenant = $verification->tenant;

        return view('verification.show', compact(
            'verification',
            'document',
            'isValid',
            'tenant'
        ));
    }

    /**
     * API endpoint for verification
     */
    public function verify(Request $request, $code)
    {
        $verification = DocumentVerification::where('verification_code', $code)->first();

        if (!$verification) {
            return response()->json([
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        $verification->recordView();
        $document = $verification->getDocument();

        if (!$document) {
            return response()->json([
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        $isValid = $verification->isValid();

        return response()->json([
            'valid' => $isValid,
            'document_type' => $verification->document_type,
            'document_number' => $document->invoice_number ?? $document->quotation_number
                ?? $document->receipt_number ?? $document->delivery_number ?? '-',
            'tenant' => $verification->tenant->company_name ?? '-',
            'date' => $document->invoice_date ?? $document->quotation_date
                ?? $document->receipt_date ?? $document->delivery_date ?? null,
            'total' => $document->total ?? $document->amount ?? 0,
            'view_count' => $verification->view_count,
            'message' => $isValid ? 'Dokumen valid dan asli' : 'Dokumen mungkin telah dimodifikasi',
        ]);
    }
}
