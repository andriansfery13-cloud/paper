<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\DocumentVerification;

trait HasVerificationCode
{
    /**
     * Boot the trait
     */
    protected static function bootHasVerificationCode()
    {
        static::creating(function ($model) {
            if (!$model->verification_code) {
                $model->verification_code = $model->generateVerificationCode();
            }
            if (!$model->document_hash) {
                $model->document_hash = $model->generateDocumentHash();
            }
        });

        static::created(function ($model) {
            $model->createDocumentVerification();
        });
    }

    /**
     * Generate unique verification code
     */
    public function generateVerificationCode()
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (static::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Generate document hash for integrity verification
     */
    public function generateDocumentHash()
    {
        $data = $this->getHashableData();
        return hash('sha256', json_encode($data));
    }

    /**
     * Get data to be included in hash
     */
    protected function getHashableData()
    {
        return [
            'id' => $this->id ?? 0,
            'number' => $this->getDocumentNumber(),
            'date' => $this->getDocumentDate(),
            'total' => $this->total ?? 0,
            'tenant_id' => $this->tenant_id,
        ];
    }

    /**
     * Get document number based on model type
     */
    protected function getDocumentNumber()
    {
        return $this->invoice_number
            ?? $this->quotation_number
            ?? $this->receipt_number
            ?? $this->delivery_number
            ?? '';
    }

    /**
     * Get document date based on model type
     */
    protected function getDocumentDate()
    {
        return $this->invoice_date
            ?? $this->quotation_date
            ?? $this->receipt_date
            ?? $this->delivery_date
            ?? now()->toDateString();
    }

    /**
     * Create document verification record
     */
    public function createDocumentVerification()
    {
        DocumentVerification::updateOrCreate(
            ['verification_code' => $this->verification_code],
            [
                'document_type' => $this->getDocumentType(),
                'document_id' => $this->id,
                'tenant_id' => $this->tenant_id,
                'document_hash' => $this->document_hash,
            ]
        );
    }

    /**
     * Get document type
     */
    protected function getDocumentType()
    {
        return strtolower(class_basename($this));
    }

    /**
     * Verify document integrity
     */
    public function verifyIntegrity()
    {
        $currentHash = $this->generateDocumentHash();
        return $this->document_hash === $currentHash;
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrl()
    {
        return url('/verify/' . $this->verification_code);
    }
}
