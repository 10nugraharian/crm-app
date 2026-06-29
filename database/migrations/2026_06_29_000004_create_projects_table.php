<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('invoice_id')->unique();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            $table->string('nama_project');
            $table->string('status')->default('ON_PROCESS');
            
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_vendor');
            $table->string('kontak')->nullable();
            
            $table->timestamps();
        });

        Schema::create('spks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            $table->uuid('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            
            $table->string('no_spk')->unique();
            $table->decimal('nilai_pekerjaan', 15, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spks');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('projects');
    }
};
