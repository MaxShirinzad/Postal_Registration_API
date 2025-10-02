<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'weight',
        'length',
        'width',
        'height',
        //'postage',
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


}
