<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Client;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        summary: "Lister les commandes (toutes pour ADMIN, personnelles pour USER)",
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste paginée des commandes',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Order'))
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Permission manquante'),
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::with([
            'client',
            'items.product'
        ]);

        if ($user && ! $user->hasRole('ADMIN')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('client_id', $user->id)
                  ->orWhereHas('client', function ($clientQuery) use ($user) {
                      $clientQuery->where('email', $user->email);
                  });
            });
        }

        $orders = $query->paginate(10);

        return OrderResource::collection($orders);
    }

    #[OA\Post(
        path: '/api/orders',
        summary: 'Créer une nouvelle commande (USER et ADMIN)',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['items'],
                properties: [
                    new OA\Property(property: 'client_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            required: ['product_id', 'quantity'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantity', type: 'integer', example: 2),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Commande créée avec succès',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Permission manquante'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $order = DB::transaction(function () use ($data, $user) {

            $clientId = $data['client_id'] ?? null;

            if (! $clientId && $user) {
                $client = Client::firstOrCreate(
                    ['email' => $user->email],
                    [
                        'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? 'User',
                    ]
                );
                $clientId = $client->id;
            }

            // Create the order
            $order = Order::create([
                'user_id' => $user?->id,
                'client_id' => $clientId,
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            // Create order items
            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                // Calculate total amount
                $total += $product->price * $item['quantity'];
            }

            // Update order total
            $order->update([
                'total_amount' => $total,
            ]);

            return $order;
        });

        // Return formatted resource with message and HTTP 201
        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource(
                $order->load(['client', 'items.product'])
            )
        ], 201);
    }

    #[OA\Get(
        path: '/api/orders/{order}',
        summary: 'Afficher une commande (ADMIN ou propriétaire)',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'ID de la commande', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la commande', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Commande introuvable'),
        ]
    )]
    public function show(Order $order)
    {
        $order->load([
            'client',
            'items.product'
        ]);

        return new OrderResource($order);
    }

    #[OA\Put(
        path: '/api/orders/{order}',
        summary: 'Mettre à jour une commande (ADMIN uniquement)',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'ID de la commande', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'paid', 'shipped', 'cancelled'], example: 'paid'),
                    new OA\Property(property: 'client_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantity', type: 'integer', example: 3),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Commande mise à jour', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé / Permission manquante'),
            new OA\Response(response: 404, description: 'Commande introuvable'),
        ]
    )]
    #[OA\Patch(
        path: '/api/orders/{order}',
        summary: 'Mettre à jour partiellement une commande (ADMIN uniquement)',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'ID de la commande', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'paid', 'shipped', 'cancelled'], example: 'shipped'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Commande mise à jour', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé / Permission manquante'),
        ]
    )]
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();

        DB::transaction(function () use ($order, $data) {
            if (isset($data['status'])) {
                $order->status = $data['status'];
            }

            if (isset($data['client_id'])) {
                $order->client_id = $data['client_id'];
            }

            if (isset($data['items'])) {
                $order->items()->delete();

                $total = 0;
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                    ]);

                    $total += $product->price * $item['quantity'];
                }

                $order->total_amount = $total;
            }

            $order->save();
        });

        return new OrderResource(
            $order->fresh()->load(['client', 'items.product'])
        );
    }

    #[OA\Delete(
        path: '/api/orders/{order}',
        summary: 'Supprimer une commande (ADMIN uniquement)',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'ID de la commande', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Commande supprimée'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé / Permission manquante'),
            new OA\Response(response: 404, description: 'Commande introuvable'),
        ]
    )]
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
