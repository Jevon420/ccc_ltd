<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('reference')->unique()->nullable()->after('id');
            $table->foreignId('client_id')->nullable()->after('reference')->constrained()->nullOnDelete();
            $table->foreignId('job_id')->nullable()->after('client_id')->constrained('work_jobs')->nullOnDelete();
            $table->foreignId('service_type_id')->nullable()->after('job_id')->constrained()->nullOnDelete();
            $table->string('title')->nullable()->after('service_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['job_id']);
            $table->dropForeign(['service_type_id']);
            $table->dropColumn(['reference', 'client_id', 'job_id', 'service_type_id', 'title']);
        });
    }
};
