<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns to product_units table for purchase limits and tiered pricing
        Schema::table('product_units', function (Blueprint $table) {
            $table->integer('min_purchase')->default(1)->after('conversion_rate');
            $table->integer('max_purchase')->nullable()->after('min_purchase');
            $table->boolean('enable_tiered_pricing')->default(false)->after('max_purchase');
        });

        // Create tiered_prices table
        Schema::create('tiered_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained('product_units')->onDelete('cascade');
            $table->integer('min_quantity');
            $table->decimal('price', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['product_unit_id', 'min_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiered_prices');
        
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['min_purchase', 'max_purchase', 'enable_tiered_pricing']);
        });
    }
};