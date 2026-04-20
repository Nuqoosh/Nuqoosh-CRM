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
    // Schema::create('documents', function (Blueprint $table) {
    //     $table->id();
    //     $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    //     $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    //     $table->foreignId('document_template_id')->constrained()->cascadeOnDelete();

    //     $table->string('type');
    //     $table->json('data');
    //     $table->string('file_path')->nullable();
    //     $table->enum('file_type', ['pdf', 'docx'])->nullable();

    //     $table->timestamps();
    // });
    Schema::create('documents', function (Blueprint $table) {
    $table->id();

    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    $table->foreignId('document_template_id')->constrained('document_templates');

    $table->longText('content');
    $table->string('pdf_path')->nullable();

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
