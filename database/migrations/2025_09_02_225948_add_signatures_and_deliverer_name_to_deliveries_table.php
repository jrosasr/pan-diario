<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('signature_beneficiary')->nullable();
            $table->string('signature_deliverer')->nullable();
            $table->string('deliverer_name')->nullable();
            $table->string('deliverer_dni')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['signature_beneficiary', 'signature_deliverer', 'deliverer_name']);
        });
    }
};
