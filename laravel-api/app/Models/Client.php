<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Client',
    title: 'Client',
    description: 'Modèle de client',
    required: ['first_name', 'last_name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'first_name', type: 'string', example: 'Jean'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Dupont'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jean.dupont@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+33612345678'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: '12 Rue de Paris, 75001 Paris'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
