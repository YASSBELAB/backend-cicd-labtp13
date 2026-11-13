<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_requests_without_api_key_are_rejected_with_401(): void
    {
        $this->getJson('/api/products')->assertStatus(401)->assertJson(['message' => 'Clé API invalide ou manquante']);
        $this->getJson('/api/categories')->assertStatus(401)->assertJson(['message' => 'Clé API invalide ou manquante']);
        $this->getJson('/api/clients')->assertStatus(401)->assertJson(['message' => 'Clé API invalide ou manquante']);
    }

    public function test_requests_with_invalid_api_key_are_rejected_with_401(): void
    {
        $headers = ['Authorization' => 'Bearer wrong-api-key'];

        $this->withHeaders($headers)->getJson('/api/products')->assertStatus(401);
        $this->withHeaders($headers)->getJson('/api/categories')->assertStatus(401);
        $this->withHeaders($headers)->getJson('/api/clients')->assertStatus(401);
    }

    public function test_requests_with_valid_api_key_succeed(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);
        Client::factory()->create();

        $headers = ['Authorization' => 'Bearer ' . config('services.api.key')];

        $this->withHeaders($headers)->getJson('/api/products')->assertStatus(200);
        $this->withHeaders($headers)->getJson('/api/categories')->assertStatus(200);
        $this->withHeaders($headers)->getJson('/api/clients')->assertStatus(200);
    }
}
