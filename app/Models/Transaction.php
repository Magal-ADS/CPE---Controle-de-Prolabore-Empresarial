<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected const LISTING_COLUMNS = [
        'id',
        'user_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'attachment_content',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAttachment(): bool
    {
        return $this->attachment_name !== null
            || $this->attachment_path !== null
            || $this->attachment_content !== null;
    }

    public function attachmentFilename(): ?string
    {
        return $this->attachment_name
            ?? ($this->attachment_path ? basename($this->attachment_path) : null);
    }

    public function scopeWithoutAttachmentContent($query)
    {
        return $query->select(self::LISTING_COLUMNS);
    }
}
