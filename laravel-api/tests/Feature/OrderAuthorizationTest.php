<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $normalUser;
    private User $otherUser;
    private Product $product;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $userRole = Role::firstOrCreate(['name' => 'USER']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole);

        $this->normalUser = User::factory()->create();
        $this->normalUser->roles()->attach($userRole);

        $this->otherUser = User::factory()->create();
        $this->otherUser->roles()->attach($userRole);

        $this->client = Client::factory()->create();
        $this->product = Product::factory()->create(['price' => 50, 'stock' => 100]);
    }

    public function test_admin_can_view_all_orders(): void
    {
        $order1 = Order::factory()->create([
            'user_id' => $this->admin->id,
            'client_id' => $this->client->id,
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $order1->id]);
        $response->assertJsonFragment(['id' => $order2->id]);
    }

    public function test_normal_user_sees_only_own_orders_in_list(): void
    {
        $orderAdmin = Order::factory()->create([
            'user_id' => $this->admin->id,
            'client_id' => $this->client->id,
        ]);

        $orderNormal = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $orderNormal->id);
    }

    public function test_normal_user_sees_orders_matched_by_client_id_or_client_email(): void
    {
        // Order where client has matching email with normalUser
        $clientWithMatchingEmail = Client::factory()->create([
            'email' => $this->normalUser->email,
        ]);

        $orderMatchedByEmail = Order::factory()->create([
            'user_id' => null,
            'client_id' => $clientWithMatchingEmail->id,
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $orderMatchedByEmail->id);

        $showResponse = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson("/api/orders/{$orderMatchedByEmail->id}");
        $showResponse->assertStatus(200);
    }

    public function test_admin_can_view_any_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $order->id);
    }

    public function test_normal_user_can_view_own_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $order->id);
    }

    public function test_normal_user_cannot_view_another_users_order(): void
    {
        $orderOfOtherUser = Order::factory()->create([
            'user_id' => $this->otherUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson("/api/orders/{$orderOfOtherUser->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_create_order(): void
    {
        $payload = [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order created successfully']);
        $this->assertDatabaseHas('orders', [
            'client_id' => $this->client->id,
            'total_amount' => 100,
        ]);
    }

    public function test_normal_user_can_create_order(): void
    {
        $payload = [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                ],
            ],
        ];

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order created successfully']);
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->normalUser->id,
            'total_amount' => 150,
        ]);
    }

    public function test_normal_user_can_create_order_without_explicit_client_id(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order created successfully']);
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->normalUser->id,
            'total_amount' => 50,
        ]);
    }

    public function test_admin_can_update_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/orders/{$order->id}", [
                'status' => 'paid',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }

    public function test_normal_user_cannot_update_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->patchJson("/api/orders/{$order->id}", [
                'status' => 'paid',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_delete_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_normal_user_cannot_delete_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->normalUser->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_unauthenticated_cannot_access_orders(): void
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $this->getJson('/api/orders')->assertStatus(401);
        $this->getJson("/api/orders/{$order->id}")->assertStatus(401);
        $this->postJson('/api/orders', [])->assertStatus(401);
        $this->patchJson("/api/orders/{$order->id}", ['status' => 'paid'])->assertStatus(401);
        $this->deleteJson("/api/orders/{$order->id}")->assertStatus(401);
    }
}
