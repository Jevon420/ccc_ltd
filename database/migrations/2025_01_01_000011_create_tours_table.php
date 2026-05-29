<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. 'dashboard_intro'
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('route_name')->nullable()->index(); // Laravel route name this tour applies to
            $table->json('role_scope')->nullable(); // array of role names this tour targets, null = all
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false); // if true, cannot be skipped
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tour_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('element')->nullable(); // CSS selector, e.g. '#sidebar', '.dashboard-card'
            $table->string('placement')->default('bottom'); // top, bottom, left, right
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(false); // must complete this step
            $table->string('action_label')->nullable(); // optional CTA button label
            $table->string('action_route')->nullable(); // optional CTA route
            $table->timestamps();
        });

        Schema::create('user_tour_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('last_step_id')->nullable()->constrained('tour_steps')->nullOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tour_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tour_progress');
        Schema::dropIfExists('tour_steps');
        Schema::dropIfExists('tours');
    }
};
