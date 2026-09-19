<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_type')->default('anonymous')->after('patient_id');
            $table->string('customer_name')->nullable()->after('customer_type');
            $table->string('customer_phone', 30)->nullable()->after('customer_name');
        });

        DB::table('sales')->whereNotNull('patient_id')->update(['customer_type' => 'patient']);
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'customer_name', 'customer_phone']);
        });
    }
};