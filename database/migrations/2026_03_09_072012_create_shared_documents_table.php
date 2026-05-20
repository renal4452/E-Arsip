<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_documents', function (Blueprint $table) {
            $table->id();
            // Pastikan baris ini TIDAK ADA ->after('id')
            $table->foreignId('category_id')->nullable()->constrained('shared_types')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->foreignId('division_id')->constrained('divisions')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();      
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_documents');
    }
};