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
        Schema::table('up_accompaniments', function (Blueprint $table) {
            // 1. **Paso crucial:** Eliminar la clave foránea existente
            // El nombre por defecto es: [nombre_tabla]_[nombre_columna]_foreign
            $table->dropForeign(['up_request_id']);

            // 2. Modificar la columna para que sea nullable
            $table->foreignId('up_request_id')->nullable()->change();

            // 3. Volver a añadir la clave foránea con la misma restricción
            // Aquí debes replicar la configuración original (e.g., onDelete('cascade'))
            $table->foreign('up_request_id')
                  ->references('id')
                  ->on('up_requests') // La tabla a la que referencia
                  ->onDelete('cascade'); // La acción original (es importante mantenerla)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('up_accompaniments', function (Blueprint $table) {
            // 1. Eliminar la clave foránea (para poder cambiar la columna)
            $table->dropForeign(['up_request_id']);

            // 2. Revertir la columna a NOT NULL
            $table->foreignId('up_request_id')->nullable(false)->change();

            // 3. Volver a añadir la clave foránea
            $table->foreign('up_request_id')
                  ->references('id')
                  ->on('up_requests')
                  ->onDelete('cascade');
        });
    }
};
