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
        Schema::create('carte_etudiantes', function (Blueprint $table) {
           $table->id();
    $table->foreignId('etudiant_id')->constrained()->onDelete('cascade');
    $table->string('qr_code')->unique();
    $table->string('statut')->default('active');
    $table->date('date_emission')->nullable();
    $table->date('date_expiration')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carte_etudiantes');
    }
};
