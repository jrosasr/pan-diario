<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_team_members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('id_number')->unique();
            $table->string('phone')->nullable();
            $table->enum('role', ['admin', 'presidency', 'stewardship', 'finance', 'welfare', 'planning']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_team_members');
    }
};
