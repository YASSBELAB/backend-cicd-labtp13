<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'E-Commerce API',
    version: '1.0.0',
    description: "Documentation de l'API RESTful Laravel (Produits, Catégories, Clients, Commandes, Authentification).\n\nSécurité :\n- Les endpoints Products (lecture), Categories et Clients sont protégés par clé API via l'en-tête `Authorization: Bearer {API_KEY}` (ou token Sanctum).\n- Les endpoints protégés d'écriture Produits et Commandes requièrent un token Bearer Sanctum."
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Serveur API Laravel'
)]
#[OA\SecurityScheme(
    securityScheme: 'apiKeyBearer',
    type: 'http',
    scheme: 'bearer',
    description: "Clé API envoyée via le header `Authorization: Bearer {API_KEY}`"
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Token Bearer Sanctum obtenu via /api/auth/login'
)]
abstract class Controller
{
    //
}
