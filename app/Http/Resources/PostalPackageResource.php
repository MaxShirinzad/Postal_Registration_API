<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PostalPackageResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="3"),
 *     @OA\Property(property="name", type="string", example="user1"),
 *     @OA\Property(property="email", type="string", format="email", example="user1@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-11 20:40:54"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-11 20:40:54"),
 *     @OA\Property(property="image", type="string", example="/users/images/user1.jpg")
 * )
 */
class PostalPackageResource extends JsonResource
{
    public static $wrap = false;

    public function toArray($request): array
    {
//        return [
//            'id' => $this->id,
//            'name' => $this->name,
//            'email' => $this->email,
//            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//            'updated_at' => $this->updated_at->toDateTimeString(),
//        ];

        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'weight' => (float) $this->weight,
            'dimensions' => [
                'length' => (float) $this->length,
                'width' => (float) $this->width,
                'height' => (float) $this->height,
            ],
            'sender' => [
                'name' => $this->sender->name,
                'mobile' => $this->sender->mobile,
                'postal_code' => $this->sender->postal_code,
                'address' => $this->sender->address,
            ],
            'receiver' => [
                'name' => $this->receiver->name,
                'mobile' => $this->receiver->mobile,
                'postal_code' => $this->receiver->postal_code,
                'address' => $this->receiver->address,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
