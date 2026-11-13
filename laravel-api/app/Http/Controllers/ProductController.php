<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Liste des produits',
        tags: ['Products'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', description: 'Terme de recherche', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'name', in: 'query', description: 'Filtrer par nom', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'price_min', in: 'query', description: 'Prix minimum', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'price_max', in: 'query', description: 'Prix maximum', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'sort', in: 'query', description: 'Champ de tri (id, name, price, stock)', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'direction', in: 'query', description: 'Direction du tri (asc, desc)', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', description: "Nombre d'éléments par page", required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product'))
            ),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
        ]
    )]
    public function index(Request $request)
    {
        // Allowed columns for sorting
        $allowedSorts = ['id', 'name', 'price', 'stock'];

        // Get sorting parameters
        $sort = $request->query('sort', 'id');
        $direction = strtolower($request->query('direction', 'asc'));

        // Validate sorting direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        // Support multiple sorting columns
        $validSorts = [];

        foreach (explode(',', $sort) as $field) {
            $field = trim($field);

            if (in_array($field, $allowedSorts)) {
                $validSorts[] = $field;
            }
        }

        if (empty($validSorts)) {
            $validSorts = ['id'];
        }

        // Pagination: default 15, minimum 1, maximum 100
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        // Eager loading + search scope
        $query = Product::with('category')
            ->search($request->query('q'));

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by minimum price
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // Filter by maximum price
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Apply sorting
        foreach ($validSorts as $field) {
            $query->orderBy($field, $direction);
        }

        // Paginate and preserve query parameters
        $products = $query->paginate($perPage)->withQueryString();

        return ProductResource::collection($products);
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Créer un nouveau produit',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro'),
                    new OA\Property(property: 'description', type: 'string', example: 'Ordinateur portable haute performance'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1299.99),
                    new OA\Property(property: 'stock', type: 'integer', example: 25),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Produit créé avec succès',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/products/{product}',
        summary: 'Afficher un produit',
        tags: ['Products'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'ID du produit',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Produit introuvable'),
        ]
    )]
    public function show(Product $product)
    {
        $product->load('category');

        return new ProductResource($product);
    }

    #[OA\Put(
        path: '/api/products/{product}',
        summary: 'Mettre à jour un produit',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'ID du produit',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro M3'),
                    new OA\Property(property: 'description', type: 'string', example: 'Version mise à jour'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1499.99),
                    new OA\Property(property: 'stock', type: 'integer', example: 15),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Produit mis à jour',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Produit introuvable'),
        ]
    )]
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ) {
        $product->update($request->validated());

        return new ProductResource(
            $product->fresh()->load('category')
        );
    }

    #[OA\Delete(
        path: '/api/products/{product}',
        summary: 'Supprimer un produit',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'ID du produit',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Produit supprimé avec succès'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Produit introuvable'),
            new OA\Response(response: 409, description: 'Produit déjà utilisé dans une commande'),
        ]
    )]
    public function destroy(Product $product)
    {
        // Check whether this product is used in an order
        $isUsed = DB::table('order_items')
            ->where('product_id', $product->id)
            ->exists();

        if ($isUsed) {
            return response()->json([
                'message' => 'Cannot delete this product because it is already used in an order.',
                'product_id' => $product->id
            ], 409);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}
