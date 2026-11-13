<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\CategoryResource;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'Liste des catégories',
        tags: ['Categories'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Category'))
            ),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
        ]
    )]
    public function index()
    {
        return CategoryResource::collection(
            Category::with('products')->paginate(10)
        );
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Créer une catégorie',
        tags: ['Categories'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Informatique'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Ordinateurs et périphériques'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Catégorie créée', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return (new CategoryResource($category->load('products')))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        summary: 'Afficher une catégorie',
        tags: ['Categories'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'ID de la catégorie', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Catégorie introuvable'),
        ]
    )]
    public function show(Category $category)
    {
        $category->load('products');

        return new CategoryResource($category);
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        summary: 'Mettre à jour une catégorie',
        tags: ['Categories'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'ID de la catégorie', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Périphériques'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Claviers, souris et écrans'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Catégorie mise à jour', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Catégorie introuvable'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => 'sometimes|nullable|string',
        ]);

        $category->update($validated);

        return new CategoryResource($category->fresh()->load('products'));
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        summary: 'Supprimer une catégorie',
        tags: ['Categories'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, description: 'ID de la catégorie', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Catégorie supprimée'),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Catégorie introuvable'),
        ]
    )]
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
