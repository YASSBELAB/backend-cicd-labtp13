<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $normalUser;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $userRole = Role::firstOrCreate(['name' => 'USER']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole);

        $this->normalUser = User::factory()->create();
        $this->normalUser->roles()->attach($userRole);

        $this->category = Category::factory()->create();
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . config('services.api.key'),
        ])->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price'],
                ],
            ]);
    }

    public function test_can_show_single_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Clavier mécanique',
            'price' => 79.99,
            'category_id' => $this->category->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . config('services.api.key'),
        ])->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'Clavier mécanique')
            ->assertJsonPath('data.price', 79.99);
    }

    public function test_admin_can_create_product(): void
    {
        Sanctum::actingAs($this->admin, ['*']);

        $response = $this->postJson('/api/products', [
            'name' => 'Clavier mécanique',
            'price' => 79.99,
            'stock' => 10,
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Clavier mécanique')
            ->assertJsonPath('data.price', 79.99);

        $this->assertDatabaseHas('products', [
            'name' => 'Clavier mécanique',
            'price' => 79.99,
        ]);
    }

    public function test_normal_user_cannot_create_product(): void
    {
        Sanctum::actingAs($this->normalUser, ['order:create', 'order:view']);

        $response = $this->postJson('/api/products', [
            'name' => 'Clavier mécanique',
            'price' => 79.99,
            'stock' => 10,
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_product(): void
    {
        Sanctum::actingAs($this->admin, ['*']);

        $product = Product::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Clavier mécanique RGB',
            'price' => 89.99,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Clavier mécanique RGB');
    }

    public function test_admin_can_delete_product(): void
    {
        Sanctum::actingAs($this->admin, ['*']);

        $product = Product::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
