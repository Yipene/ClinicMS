<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Clinic MS');
            $table->string('legal_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone_primary', 30)->nullable();
            $table->string('phone_secondary', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('currency', 3)->default('XOF');
            $table->decimal('cash_register_threshold', 14, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedSmallInteger('delivery_delay_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('name');
            $table->string('dci')->nullable()->index();
            $table->enum('category', ['medicament', 'consommable', 'materiel'])->default('medicament');
            $table->string('unit', 20)->default('unité');
            $table->decimal('purchase_price', 14, 2)->default(0);
            $table->decimal('sale_price', 14, 2)->default(0);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('min_stock_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['draft', 'ordered', 'partial', 'received', 'cancelled'])->default('draft');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('expected_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity_ordered');
            $table->unsignedInteger('quantity_received')->default(0);
            $table->decimal('unit_price', 14, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'purchase', 'sale', 'internal', 'expired', 'adjustment', 'cancellation_return', 'inventory',
            ]);
            $table->integer('quantity');
            $table->decimal('unit_purchase_price', 14, 2)->nullable();
            $table->decimal('unit_sale_price', 14, 2)->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->enum('module', [
                'caisse', 'pharmacie', 'consultation', 'examen', 'hospitalisation',
                'intervention', 'accouchement', 'bloc',
            ])->default('caisse');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'partially_cancelled', 'cancelled'])->default('completed');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->string('act_type')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->enum('method', ['cash', 'mobile_money', 'transfer'])->default('cash');
            $table->string('reference')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();
        });

        Schema::create('sale_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['partial', 'full']);
            $table->decimal('amount', 14, 2);
            $table->text('reason');
            $table->json('items')->nullable();
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('department')->nullable();
            $table->enum('type', ['standard', 'vip', 'icu', 'maternity'])->default('standard');
            $table->decimal('daily_rate', 14, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('operating_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('half_day_rate', 14, 2)->default(0);
            $table->decimal('full_day_rate', 14, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('consulted_at');
            $table->text('reason')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prescription')->nullable();
            $table->decimal('fee', 14, 2)->default(0);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['biology', 'imaging', 'other'])->default('biology');
            $table->string('label');
            $table->enum('status', ['prescribed', 'in_progress', 'completed', 'cancelled'])->default('prescribed');
            $table->timestamp('prescribed_at');
            $table->timestamp('result_at')->nullable();
            $table->text('result')->nullable();
            $table->decimal('fee', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('admitted_at');
            $table->timestamp('discharged_at')->nullable();
            $table->enum('status', ['admitted', 'transferred', 'discharged', 'cancelled'])->default('admitted');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surgeon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('anesthesiologist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('operating_room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('scheduled_at');
            $table->text('operative_report')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->decimal('fee', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['normal', 'cesarean'])->default('normal');
            $table->unsignedSmallInteger('gestational_weeks')->nullable();
            $table->timestamp('delivered_at');
            $table->json('newborn_info')->nullable();
            $table->decimal('fee', 14, 2)->default(0);
            $table->text('postnatal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('operating_room_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operating_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reserved_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_client')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->enum('billing_type', ['half_day', 'full_day'])->default('half_day');
            $table->decimal('amount', 14, 2)->default(0);
            $table->enum('status', ['reserved', 'in_use', 'completed', 'cancelled'])->default('reserved');
            $table->text('convention_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('expected_quantity');
            $table->unsignedInteger('counted_quantity');
            $table->integer('variance');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        $tables = [
            'audit_logs', 'inventory_items', 'inventories',
            'operating_room_bookings', 'deliveries', 'surgeries', 'hospitalizations',
            'exams', 'consultations', 'operating_rooms', 'rooms',
            'sale_cancellations', 'payments', 'sale_items', 'sales',
            'stock_movements', 'purchase_order_items', 'purchase_orders',
            'products', 'suppliers', 'patients', 'clinic_settings',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
