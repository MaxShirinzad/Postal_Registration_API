<?php

namespace App\Models;

use App\Enums\UserType;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "name", "email", "password"},
 *     @OA\Property(property="id", type="integer", example="3"),
 *     @OA\Property(property="name", type="string", example="user1"),
 *     @OA\Property(property="email", type="string", format="email", example="user1@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-11 20:40:54"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-11 20:40:54"),
 *     @OA\Property(property="image", type="string", example="/users/images/user1.jpg")
 * )
 *
 * App\Models\User
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property int $type_id
 * @property string|null $email
 * @property string|null $image
 * @property boolean $IsActivated
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->type_id, [
            UserType::SUPERADMIN->value,
            UserType::ADMIN->value
        ]);
    }

    public function imagePublicLink(): ?string
    {
        if ($this->image){
            return url('/') . '/users/images/' . $this->image ?? null;
        }
        return null;
    }


}
