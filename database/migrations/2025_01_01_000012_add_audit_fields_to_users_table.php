<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Avatar / profile
            $table->string('avatar')->nullable()->after('email_verified_at');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('position')->nullable()->after('phone'); // Job title
            $table->boolean('is_active')->default(true)->after('position');

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable()->after('remember_token');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->unsignedBigInteger('restored_by')->nullable()->after('deleted_by');
            $table->timestamp('restored_at')->nullable()->after('restored_by');

            // Soft deletes
            $table->softDeletes()->after('restored_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'phone', 'position', 'is_active',
                'created_by', 'updated_by', 'deleted_by', 'restored_by', 'restored_at',
                'deleted_at',
            ]);
        });
    }
};
