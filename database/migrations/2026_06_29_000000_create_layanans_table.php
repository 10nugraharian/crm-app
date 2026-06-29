<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_layanan');
            $table->decimal('harga_modal', 15, 2);
            $table->decimal('harga_pokok', 15, 2);
            $table->decimal('komisi_sales', 15, 2);
            $table->decimal('komisi_sso', 15, 2)->default(50000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
