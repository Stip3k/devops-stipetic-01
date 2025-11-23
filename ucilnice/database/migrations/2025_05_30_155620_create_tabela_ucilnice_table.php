<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zazeni.
     */
    public function up(): void
    {
        Schema::create('tabela_ucilnice', function (Blueprint $table) {
            $table->id();
            $table->string('id_ucilnice', 50)->unique();
            $table->integer('kapaciteta');
            $table->string('vrsta_ucilnice', 50);
            $table->string('skrbnik', 50)->nullable();
            $table->timestamps();
            
            $table->index('id_ucilnice');
            $table->index('vrsta_ucilnice');
        });
    }

    /**
     * Povrni migracijo nazaj.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabela_ucilnice');
    }
};