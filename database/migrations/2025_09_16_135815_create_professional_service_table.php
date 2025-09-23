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
        Schema::create('professional_service', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('professional_id')->constrained()->onDelete('cascade');
            $table->integer('limit')->default(1)->comment('Número máximo de veces que un profesional puede ofrecer este servicio');
            $table->boolean('is_free')->default(false)->comment('Indica si el servicio es gratuito');
            $table->integer('price')->default(0)->comment('Precio base en centavos');
            $table->integer('discount_percentage')->default(0)->comment('Porcentaje de descuento, 0-100');
            $table->integer('discount_amount')->default(0)->comment('Monto de descuento en centavos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_services');
    }
};
