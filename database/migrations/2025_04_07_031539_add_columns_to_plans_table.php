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
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('pdf_files')->default(0); // Número de archivos PDF permitidos
            $table->integer('urls')->default(0); // Número de URLs permitidas
            $table->integer('text_limit')->default(0); // Limite de texto en palabras
            $table->integer('questions_limit')->default(0); // Límite de preguntas

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('pdf_files');
            $table->dropColumn('urls');
            $table->dropColumn('text_limit');
            $table->dropColumn('questions_limit');
        });
    }
};
