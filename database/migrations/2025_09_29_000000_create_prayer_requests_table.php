<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        if (!Schema::hasTable('prayer_requests')) {
            Schema::create('prayer_requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->enum('request_type', [
                    'marriage',
                    'family',
                    'parenthood',
                    'premarital_counseling',
                    'anxiety_stress_depression',
                    'anger',
                    'divorce',
                    'grief',
                    'struggle_with_sin',
                    'self_esteem',
                    'doubts',
                    'confusion',
                    'other',
                ]);
                $table->text('description');
                $table->enum('petition_type', ['prayer', 'counseling']);

                // fecha y hora appointment_date
                $table->dateTime('appointment_date')->nullable();
                $table->date('date')->nullable();
                $table->enum('status', [
                    'attended',
                    'pending',
                    'cancelled',
                    'reassigned',
                    'rescheduled',
                ])->default('pending');
                $table->foreignId('team_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
