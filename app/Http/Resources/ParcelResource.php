<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParcelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'formatted_tracking_code' => $this->formatted_tracking_code,
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
