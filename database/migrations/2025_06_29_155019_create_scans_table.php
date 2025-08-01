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
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_heure')->useCurrent(); // date et heure du scan
            $table->string('statut_acces'); // accepté / refusé
            $table->boolean('validation')->default(false); // validé par l’agent ou pas
            $table->string('commentaire')->nullable(); // info complémentaire (ex: “carte expirée”)
            $table->foreignId('carte_etudiante_id')->constrained()->onDelete('cascade'); // lien vers la carte
             $table->foreignId('agent_id')->nullable()->constrained('personnel_securites')->onDelete('set null'); // agent de sécurité
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
