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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Main task info
            $table->string('title'); // e.g. "Implement login feature"
            $table->text('description')->nullable();

            // Task status & priority
            $table->enum('status', ['pending', 'in_progress', 'in_review', 'completed', 'blocked'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Dates
            $table->date('due_date')->nullable();

            // Work estimation
            $table->integer('estimated_hours')->default(0);

            // Relationships
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // belongs to a project
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // assigned user
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // creator user
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
