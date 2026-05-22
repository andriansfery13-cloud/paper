<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_code',
        'document_type',
        'document_id',
        'tenant_id',
        'document_hash',
        'view_count',
        'last_viewed_at',
        'last_viewed_ip',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getDocument()
    {
        $modelClass = $this->getDocumentModelClass();
        if ($modelClass) {
            return $modelClass::withoutGlobalScopes()->find($this->document_id);
        }
        return null;
    }

    protected function getDocumentModelClass()
    {
        $mapping = [
            'invoice' => Invoice::class,
            'quotation' => Quotation::class,
            'receipt' => Receipt::class,
            'deliverynote' => DeliveryNote::class,
        ];

        return $mapping[strtolower($this->document_type)] ?? null;
    }

    public function recordView()
    {
        $this->increment('view_count');
        $this->update([
            'last_viewed_at' => now(),
            'last_viewed_ip' => request()->ip(),
        ]);
    }

    public function isValid()
    {
        $document = $this->getDocument();
        if (!$document) {
            return false;
        }

        return $document->document_hash === $this->document_hash;
    }
}
