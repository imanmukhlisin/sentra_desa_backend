<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();

            $table->enum('category', [
                'umum', 'pembangunan', 'kesehatan', 'pendidikan',
                'ekonomi', 'lingkungan', 'budaya', 'teknologi', 'wisata', 'nasional',
            ])->default('umum');

            $table->string('author_name')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('read_time')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->json('tags')->nullable();

            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['village_id', 'status']);
            $table->index('is_featured');
            $table->index('slug');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
