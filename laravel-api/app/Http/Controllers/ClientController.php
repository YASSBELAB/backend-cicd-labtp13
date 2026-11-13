<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class ClientController extends Controller
{
    #[OA\Get(
        path: '/api/clients',
        summary: 'Liste des clients',
        tags: ['Clients'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succès',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Client'))
            ),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
        ]
    )]
    public function index()
    {
        return ClientResource::collection(
            Client::with('orders')->paginate(10)
        );
    }

    #[OA\Post(
        path: '/api/clients',
        summary: 'Créer un client',
        tags: ['Clients'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['first_name', 'last_name', 'email'],
                properties: [
                    new OA\Property(property: 'first_name', type: 'string', example: 'Jean'),
                    new OA\Property(property: 'last_name', type: 'string', example: 'Dupont'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jean.dupont@example.com'),
                    new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+33612345678'),
                    new OA\Property(property: 'address', type: 'string', nullable: true, example: '12 Rue de Paris'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client créé', content: new OA\JsonContent(ref: '#/components/schemas/Client')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/clients/{client}',
        summary: 'Afficher un client',
        tags: ['Clients'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, description: 'ID du client', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès', content: new OA\JsonContent(ref: '#/components/schemas/Client')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Client introuvable'),
        ]
    )]
    public function show(Client $client)
    {
        $client->load('orders');

        return new ClientResource($client);
    }

    #[OA\Put(
        path: '/api/clients/{client}',
        summary: 'Mettre à jour un client',
        tags: ['Clients'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, description: 'ID du client', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'first_name', type: 'string', example: 'Jean'),
                    new OA\Property(property: 'last_name', type: 'string', example: 'Dupont'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jean.dupont@example.com'),
                    new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+33612345678'),
                    new OA\Property(property: 'address', type: 'string', nullable: true, example: '12 Rue de Paris'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Client mis à jour', content: new OA\JsonContent(ref: '#/components/schemas/Client')),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Client introuvable'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ]
    )]
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($client->id),
            ],
            'phone' => 'sometimes|nullable|string|max:30',
            'address' => 'sometimes|nullable|string',
        ]);

        $client->update($validated);

        return new ClientResource($client->fresh());
    }

    #[OA\Delete(
        path: '/api/clients/{client}',
        summary: 'Supprimer un client',
        tags: ['Clients'],
        security: [['apiKeyBearer' => []], ['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'client', in: 'path', required: true, description: 'ID du client', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Client supprimé'),
            new OA\Response(response: 401, description: 'Clé API invalide ou manquante'),
            new OA\Response(response: 404, description: 'Client introuvable'),
        ]
    )]
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->noContent();
    }
}
