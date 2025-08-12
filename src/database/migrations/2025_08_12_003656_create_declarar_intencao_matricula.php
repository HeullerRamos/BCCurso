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
        Schema::create('declarar_intencao_matricula', function (Blueprint $table) {
            $table->id();
            $table->integer('ano')->unsigned();
            $table->integer('periodo')->unsigned();
            $table->foreignId('aluno_id')->constrained('aluno');
            $table->timestamps();
        });

        Schema::create('declarar_intencao_matricula_disciplina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declarar_intencao_matricula_id')->constrained('declarar_intencao_matricula')->onDelete('cascade');
            $table->foreignId('intencao_matricula_id')->constrained('intencao_matricula')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarar_intencao_matricula_disciplina');
        Schema::dropIfExists('declarar_intencao_matricula');
    }
};
