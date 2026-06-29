<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('sales_id')->nullable();
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('set null');
            
            $table->uuid('sso_id')->nullable();
            $table->foreign('sso_id')->references('id')->on('users')->onDelete('set null');
            
            $table->enum('status_leads', ['NEW', 'CONTACTED', 'RESPONSE', 'QUOTATION', 'WON', 'LOST'])->default('NEW');
            $table->enum('kualifikasi', ['HOT', 'WARM', 'COLD', 'UNQUALIFIED'])->nullable();
            
            $table->string('nama_perusahaan');
            $table->string('jenis_perusahaan')->nullable();
            $table->string('tingkat_kualifikasi')->nullable();
            $table->string('sub_klasifikasi')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('nama_pic')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->json('wilayah')->nullable(); // Provinsi & Kota
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
