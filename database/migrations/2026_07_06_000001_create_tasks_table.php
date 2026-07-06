<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Which company's work this task is — the core of the
            // "kis company ka task kiya" record requirement.
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Who the task is assigned to. If that user is later deleted,
            // keep the task record (set null) so history survives.
            $table->foreignId('assigned_to')->nullable()
                  ->constrained('users')->nullOnDelete();

            // Who created/assigned the task — the audit half of the record.
            $table->foreignId('assigned_by')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->enum('status', ['pending', 'in_progress', 'completed'])
                  ->default('pending');

            $table->date('due_date')->nullable();

            // Set when status becomes completed; cleared if reverted.
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // The two most common lookups: "my tasks" and "company tasks by status"
            $table->index(['assigned_to', 'status']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};