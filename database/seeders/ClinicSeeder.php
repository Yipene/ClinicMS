<?php

namespace Database\Seeders;

use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Product;
use App\Models\OperatingRoom;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        ClinicSetting::current();

        $permissions = [
            'dashboard.view',
            'caisse.manage', 'caisse.reports',
            'pharmacie.sell', 'pharmacie.supply', 'pharmacie.cancel',
            'stock.manage', 'stock.inventory',
            'patients.manage',
            'consultations.manage', 'exams.manage',
            'hospitalizations.manage', 'surgeries.manage',
            'deliveries.manage', 'bloc.manage',
            'users.manage', 'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'administrateur' => $permissions,
            'medecin' => [
                'dashboard.view', 'patients.manage',
                'consultations.manage', 'exams.manage',
                'hospitalizations.manage', 'surgeries.manage', 'deliveries.manage',
            ],
            'caissier' => [
                'dashboard.view', 'caisse.manage', 'caisse.reports',
            ],
            'pharmacien' => [
                'dashboard.view', 'pharmacie.sell', 'pharmacie.supply',
                'pharmacie.cancel',
            ],
            'gestionnaire_stock' => [
                'dashboard.view', 'stock.manage', 'stock.inventory',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@clinic.local'],
            [
                'name' => 'Administrateur',
                'phone' => '+226 00 00 00 00',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrateur');

        $users = [
            ['email' => 'medecin@clinic.local', 'name' => 'Dr. Ibrahim Ouédraogo', 'role' => 'medecin'],
            ['email' => 'caissier@clinic.local', 'name' => 'Aminata Koné', 'role' => 'caissier'],
            ['email' => 'pharmacien@clinic.local', 'name' => 'Moussa Sanou', 'role' => 'pharmacien'],
            ['email' => 'stock@clinic.local', 'name' => 'Gestionnaire du stock', 'role' => 'gestionnaire_stock'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($data['role']);
        }

        Supplier::firstOrCreate(
            ['name' => 'Pharma Distribution BF'],
            [
                'contact_name' => 'Jean-Pierre Zongo',
                'phone' => '+226 70 00 00 01',
                'email' => 'contact@pharmadist.bf',
                'delivery_delay_days' => 5,
            ]
        );

        $products = [
            ['sku' => 'MED-001', 'name' => 'Paracétamol 500mg', 'dci' => 'Paracétamol', 'purchase_price' => 150, 'sale_price' => 300, 'stock_quantity' => 500],
            ['sku' => 'MED-002', 'name' => 'Amoxicilline 500mg', 'dci' => 'Amoxicilline', 'purchase_price' => 800, 'sale_price' => 1500, 'stock_quantity' => 8],
            ['sku' => 'MED-003', 'name' => 'Artéméther-Luméfantrine', 'dci' => 'Artéméther', 'purchase_price' => 1200, 'sale_price' => 2500, 'stock_quantity' => 45],
            ['sku' => 'CON-001', 'name' => 'Seringue 5ml', 'dci' => null, 'category' => 'consommable', 'purchase_price' => 50, 'sale_price' => 150, 'stock_quantity' => 200],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                array_merge([
                    'category' => 'medicament',
                    'unit' => 'boîte',
                    'min_stock_level' => 20,
                    'is_active' => true,
                ], $product)
            );
        }

        Patient::firstOrCreate(
            ['code' => 'PAT-00001'],
            [
                'first_name' => 'Fatou',
                'last_name' => 'Traoré',
                'phone' => '+226 70 12 34 56',
                'gender' => 'F',
                'birth_date' => '1990-05-15',
            ]
        );

        $rooms = [
            ['code' => 'CH-101', 'name' => 'Chambre 101', 'department' => 'Médecine', 'daily_rate' => 15000],
            ['code' => 'CH-102', 'name' => 'Chambre 102', 'department' => 'Médecine', 'daily_rate' => 15000],
            ['code' => 'CH-VIP', 'name' => 'Suite VIP', 'department' => 'VIP', 'type' => 'vip', 'daily_rate' => 45000],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(['code' => $room['code']], $room);
        }

        $blocs = [
            ['code' => 'BLOC-1', 'name' => 'Bloc opératoire 1', 'half_day_rate' => 150000, 'full_day_rate' => 280000],
            ['code' => 'BLOC-2', 'name' => 'Bloc opératoire 2', 'half_day_rate' => 120000, 'full_day_rate' => 220000],
        ];
        foreach ($blocs as $bloc) {
            OperatingRoom::firstOrCreate(['code' => $bloc['code']], $bloc);
        }

        $this->command?->info(config('app.name').' initialisé.');
        $this->command?->info('Connexion admin : admin@clinic.local / password');
    }
}
