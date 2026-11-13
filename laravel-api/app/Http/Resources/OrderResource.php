<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderItemResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'client' => new ClientResource(
                $this->whenLoaded('client')
            ),

            'total_amount' => $this->total_amount,

            'status' => $this->status,

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
