<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Factories\HasFactory;

return new class extends Migration
{
    use HasFactory;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doc_versions', function (Blueprint $table) {
            $table->id(); // id_version

            $table->foreignId('doc_id')
                ->constrained('documents')
                ->onDelete('cascade');

            $table->integer('version_number');
            $table->string('file_path');
            $table->bigInteger('file_size');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_versions');
    }
};
