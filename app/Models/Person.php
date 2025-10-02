<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'mobile',
        'postal_code',
        'address',
    ];

    public function sentPostalPackages(): HasMany
    {
        return $this->hasMany(PostalPackage::class, 'sender_id');
    }

    public function receivedPostalPackages(): HasMany
    {
        return $this->hasMany(PostalPackage::class, 'receiver_id');
    }
}
