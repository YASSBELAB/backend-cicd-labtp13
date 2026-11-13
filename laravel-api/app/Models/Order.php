<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Order',
    title: 'Order',
    description: 'Modèle de commande',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'client', ref: '#/components/schemas/Client'),
        new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 159.98),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'paid', 'shipped', 'cancelled'], example: 'pending'),
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
