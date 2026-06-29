<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            $table->uuid('sales_id');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('no_quotation')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->enum('status_approval', ['APPROVED', 'PENDING_FINANCE', 'REJECTED'])->default('PENDING_FINANCE');
            
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('quotation_id');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            
            $table->uuid('layanan_id');
            $table->foreign('layanan_id')->references('id')->on('layanans')->onDelete('cascade');
            
            $table->integer('qty');
            $table->decimal('harga_jual_input', 15, 2);
            $table->decimal('refund_sales_margin', 15, 2); // Kalkulasi: (Harga Jual - Pokok) * Qty
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
