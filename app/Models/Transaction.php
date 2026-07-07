<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    private const ATTACHMENT_BASE64_PREFIX = 'base64:';

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

    protected function attachmentContent(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_resource($value)) {
                    $value = stream_get_contents($value);
                }

                if (! is_string($value)) {
                    return $value;
                }

                return $this->decodeAttachmentContent($value);
            },
            set: function ($value) {
                if ($value === null) {
                    return null;
                }

                if (is_resource($value)) {
                    $value = stream_get_contents($value);
                }

                if (! is_string($value) || str_starts_with($value, self::ATTACHMENT_BASE64_PREFIX)) {
                    return $value;
                }

                return self::ATTACHMENT_BASE64_PREFIX.base64_encode($value);
            },
        );
    }

    private function decodeAttachmentContent(string $value): string
    {
        if (str_starts_with($value, self::ATTACHMENT_BASE64_PREFIX)) {
            $decodedValue = base64_decode(substr($value, strlen(self::ATTACHMENT_BASE64_PREFIX)), true);

            return $decodedValue === false ? $value : $decodedValue;
        }

        if (str_starts_with($value, '\\x') && ctype_xdigit(substr($value, 2))) {
            $decodedValue = hex2bin(substr($value, 2));

            if (is_string($decodedValue)) {
                return $this->decodeAttachmentContent($decodedValue);
            }
        }

        return $this->decodeBase64Attachment($value) ?? $value;
    }

    private function decodeBase64Attachment(string $value): ?string
    {
        $normalizedValue = preg_replace('/\s+/', '', $value);

        if (! is_string($normalizedValue) || $normalizedValue === '') {
            return null;
        }

        $decodedValue = base64_decode($normalizedValue, true);

        if (! is_string($decodedValue) || ! $this->looksLikeSupportedAttachment($decodedValue)) {
            return null;
        }

        return $decodedValue;
    }

    private function looksLikeSupportedAttachment(string $value): bool
    {
        return str_starts_with($value, '%PDF-')
            || str_starts_with($value, "\xFF\xD8\xFF")
            || str_starts_with($value, "\x89PNG\r\n\x1A\n")
            || str_starts_with($value, 'RIFF');
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
