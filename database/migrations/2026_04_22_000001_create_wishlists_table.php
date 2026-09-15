<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('Desa yang membuat wishlist');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('unit', 32)->nullable();
            $table->date('needed_by')->nullable();
            $table->string('image')->nullable();

            $table->enum('status', ['open', 'fulfilled', 'closed'])
                ->default('open')
                ->index();

            $table->unsignedInteger('notified_count')->default(0);
            $table->timestamp('notified_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
