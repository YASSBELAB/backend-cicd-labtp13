<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class TokenAbilityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $normalUser;
    private Client $client;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $userRole = Role::firstOrCreate(['name' => 'USER']);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);
        $this->admin->roles()->attach($adminRole);

        $this->normalUser = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);
        $this->normalUser->roles()->attach($userRole);

        $this->client = Client::factory()->create();
        $this->product = Product::factory()->create(['price' => 20, 'stock' => 50]);
    }

    public function test_login_creates_token_with_correct_abilities(): void
    {
        // Admin login
        $adminRes = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'device_name' => 'AdminDevice',
        ]);
        $adminRes->assertStatus(200);
        $adminToken = $this->admin->tokens()->first();
        $this->assertEquals(['*'], $adminToken->abilities);

        // Normal user login
        $userRes = $this->postJson('/api/auth/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
            'device_name' => 'UserDevice',
        ]);
        $userRes->assertStatus(200);
        $userToken = $this->normalUser->tokens()->first();
        $this->assertEquals(['order:create', 'order:view'], $userToken->abilities);
    }

    public function test_token_without_order_create_ability_cannot_create_order(): void
    {
        Sanctum::actingAs($this->normalUser, ['order:view']);

        $response = $this->postJson('/api/orders', [
            'client_id' => $this->client->id,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Permission manquante', $response->json('message'));
    }

    public function test_token_without_order_view_ability_cannot_view_orders(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        Sanctum::actingAs($this->normalUser, ['order:create']);

        $listRes = $this->getJson('/api/orders');
        $listRes->assertStatus(403);
        $this->assertEquals('Permission manquante', $listRes->json('message'));

        $showRes = $this->getJson("/api/orders/{$order->id}");
        $showRes->assertStatus(403);
        $this->assertEquals('Permission manquante', $showRes->json('message'));
    }

    public function test_token_with_user_abilities_cannot_update_or_delete_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($this->normalUser, ['order:create', 'order:view']);

        // Cannot update
        $updateRes = $this->patchJson("/api/orders/{$order->id}", ['status' => 'paid']);
        $updateRes->assertStatus(403);
        $this->assertEquals('Permission manquante', $updateRes->json('message'));

        // Cannot delete
        $deleteRes = $this->deleteJson("/api/orders/{$order->id}");
        $deleteRes->assertStatus(403);
        $this->assertEquals('Permission manquante', $deleteRes->json('message'));
    }

    public function test_admin_token_with_wildcard_can_perform_all_actions(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($this->admin, ['*']);

        // View
        $this->getJson('/api/orders')->assertStatus(200);
        $this->getJson("/api/orders/{$order->id}")->assertStatus(200);

        // Update
        $this->patchJson("/api/orders/{$order->id}", ['status' => 'paid'])
            ->assertStatus(200);

        // Delete
        $this->deleteJson("/api/orders/{$order->id}")
            ->assertStatus(204);
    }
}
