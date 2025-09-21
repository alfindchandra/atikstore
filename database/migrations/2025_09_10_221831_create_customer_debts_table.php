<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_debts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('debt_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->text('items_purchased')->nullable(); // JSON atau text untuk menyimpan barang yang dibeli
            $table->enum('status', ['active', 'paid', 'partially_paid'])->default('active');
            $table->date('debt_date');
            $table->date('due_date')->nullable();
            $table->timestamps();
            
            $table->index(['customer_name', 'status']);
            $table->index('debt_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_debts');
    }
};