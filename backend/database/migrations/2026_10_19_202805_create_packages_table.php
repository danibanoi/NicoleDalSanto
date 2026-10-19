<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');            // glutei, total-body, tonificazione, ...
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents');
            $table->unsignedSmallInteger('duration_weeks')->default(4);
            $table->unsignedTinyInteger('sessions_per_week')->default(2);
            $table->string('level')->default('Tutti i livelli');
            $table->string('mode')->default('Studio');   // Studio, Online, Studio + Online
            $table->json('features')->nullable();        // ["...", "..."]
            $table->json('program')->nullable();         // [{"title": "...", "text": "..."}]
            $table->string('color')->default('pink');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
