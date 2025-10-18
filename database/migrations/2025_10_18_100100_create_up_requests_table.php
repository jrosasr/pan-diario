<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_requests', function (Blueprint $table) {
            $table->id();
            $table->string('target_beneficiary');
            $table->string('beneficiary_name');
            $table->string('identification_card');
            $table->string('contact_phone')->nullable();
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->string('help_type');
            $table->text('observations')->nullable();
            $table->timestamp('request_date');
            $table->timestamp('registration_date')->useCurrent();
            $table->foreignId('team_member_id')
                ->constrained('up_team_members')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_requests');
    }
};
