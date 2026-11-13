<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_items_and_client(): void
    {
        // Create a client
        $client = Client::factory()->create();

        // Create an order
        $order = Order::factory()->for($client)->create([
            'total_amount' => 70,
        ]);

        // Create products
        $product1 = Product::factory()->create([
            'price' => 20,
        ]);

        $product2 = Product::factory()->create([
            'price' => 30,
        ]);

        // Create three order items
        OrderItem::factory()->for($order)->for($product1)->create([
            'quantity' => 2,
            'unit_price' => 20,
        ]);

        OrderItem::factory()->for($order)->for($product2)->create([
            'quantity' => 1,
            'unit_price' => 30,
        ]);

        // Reload relationships
        $order->refresh()->load(['client', 'items']);

        // Test client relationship
        $this->assertInstanceOf(Client::class, $order->client);

        // Test order items count
        $this->assertCount(2, $order->items);

        // Test total amount calculation
        $calculatedTotal = $order->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $this->assertEquals(70, $calculatedTotal);
        $this->assertEquals(
            $order->total_amount,
            $calculatedTotal
        );
    }
}
