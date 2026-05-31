<?php

use App\Traits\HasAuditFields;
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
        Schema::create('job_reports', function (Blueprint $table) {
            $table->id();
            HasAuditFields::addAuditColumns($table);
            $table->foreignId('job_id')->constrained('work_jobs')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('work_performed')->nullable();
            $table->text('issues_encountered')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_reports');
    }
};
