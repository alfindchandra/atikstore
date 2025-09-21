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
        // Update transactions table to handle additional costs
        Schema::table('transactions', function (Blueprint $table) {
            // Ensure tax_amount column exists (for storing additional costs)
            if (!Schema::hasColumn('transactions', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
            }
            
            // Add notes column for additional information
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        // Update transaction_details table to handle custom prices and temporary products
        Schema::table('transaction_details', function (Blueprint $table) {
            // Make product_id and unit_id nullable for temporary products
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('unit_id')->nullable()->change();
            
            // Add notes column for temporary product information
            if (!Schema::hasColumn('transaction_details', 'notes')) {
                $table->text('notes')->nullable()->after('subtotal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_details', 'notes')) {
                $table->dropColumn('notes');
            }
            
            // Revert product_id and unit_id to be required
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->unsignedBigInteger('unit_id')->nullable(false)->change();
        });
    }
};