<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'weight',
        'length',
        'width',
        'height',
        'tracking_code',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'receiver_id');
    }

    protected static function boot()
    {
        parent::boot();

        //  بررسی یکتایی کد رهگیری
        static::creating(function ($parcel) {
            if (static::where('tracking_code', $parcel->tracking_code)->exists()) {
                throw new \Exception('این کد رهگیری قبلاً استفاده شده است');
            }
        });
    }

    /**
     * اعتبارسنجی فرمت کد رهگیری
     */
    public static function isValidIranianTrackingCode(string $code): bool
    {
        return preg_match('/^\d{24}$/', $code) === 1;
    }

    /**
     * فرمت کردن کد رهگیری برای نمایش (هر 4 رقم با - جدا شود)
     */
    public function getFormattedTrackingCodeAttribute(): string
    {
        return implode('-', str_split($this->tracking_code, 4));
    }
}
