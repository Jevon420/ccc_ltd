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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            HasAuditFields::addAuditColumns($table);
            $table->string('name');
            $table->enum('type', ['vehicle', 'machinery', 'tool', 'ppe', 'other'])->default('other');
            $table->string('serial_number')->nullable();
            $table->string('make_model')->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
