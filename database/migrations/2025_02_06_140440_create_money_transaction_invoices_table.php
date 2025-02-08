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
        Schema::create('money_transaction_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('superadmin_id')->constrained('users')->onDelete('cascade')->nullable()->default();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->decimal('amount', 10, 2)->default('0');
            $table->string('transaction_id')->unique()->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_transaction_invoices');
    }
};
