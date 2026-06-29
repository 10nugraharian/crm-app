<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengechekan_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->text('catatan_pengechekan');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengechekan_logs');
    }
};
