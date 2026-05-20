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
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // id_doc
            $table->string('no_doc')->unique();
            $table->string('title');

            $table->foreignId('doc_type_id')
                ->constrained('doc_types')
                ->onDelete('cascade');

            $table->foreignId('division_id')
                ->constrained('divisions')
                ->onDelete('cascade');

            $table->enum('status', ['pending', 'revisi', 'approved'])
                ->default('pending');

            $table->integer('current_version')->default(1);

            $table->foreignId('auditor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('auditor_note')->nullable();
            $table->timestamp('approve_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
