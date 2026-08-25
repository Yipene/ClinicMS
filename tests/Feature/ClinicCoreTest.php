<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\ClinicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicCoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ClinicSeeder::class);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_and_settings(): void
    {
        $admin = User::where('email', 'admin@clinic.local')->first();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk();
    }

    public function test_cashier_can_create_sale(): void
    {
        $caissier = User::where('email', 'caissier@clinic.local')->first();
        $product = Product::where('stock_quantity', '>', 0)->first();

        $this->assertNotNull($product);

        $response = $this->actingAs($caissier)->post(route('caisse.sales.store'), [
            'module' => 'caisse',
            'items' => [
                [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => 1,
                    'unit_price' => $product->sale_price,
                ],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => $product->sale_price],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', ['module' => 'caisse', 'status' => 'completed']);
    }

    public function test_doctor_can_create_patient(): void
    {
        $medecin = User::where('email', 'medecin@clinic.local')->first();

        $this->actingAs($medecin)
            ->post(route('patients.store'), [
                'first_name' => 'Awa',
                'last_name' => 'Traoré',
                'phone' => '+22670123456',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('patients', ['first_name' => 'Awa', 'last_name' => 'Traoré']);
    }
}
