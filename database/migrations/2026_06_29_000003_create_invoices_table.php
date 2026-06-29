<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('quotation_id')->unique();
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            
            $table->decimal('total_amount', 15, 2);
            $table->decimal('persentase_dp', 5, 2);
            
            $table->enum('status_pembayaran', ['UNPAID', 'PAID_DP', 'FULL_PAID'])->default('UNPAID');
            $table->enum('status_approval', ['APPROVED', 'PENDING_FINANCE'])->default('PENDING_FINANCE');
            
            $table->timestamps();
        });

        Schema::create('komisis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('sales_id');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->uuid('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            $table->decimal('total_refund_sales', 15, 2);
            $table->decimal('total_komisi_fix', 15, 2);
            
            $table->enum('status_pencairan', ['PENDING', 'DISBURSED'])->default('PENDING');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisis');
        Schema::dropIfExists('invoices');
    }
};
