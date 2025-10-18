<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('up_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')
                ->constrained('up_requests')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('up_documents');
    }
};
