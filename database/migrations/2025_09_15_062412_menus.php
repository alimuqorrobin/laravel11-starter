<?php

// database/migrations/2025_01_01_000003_create_menus_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('route')->nullable(); // route name or url
            $table->string('icon')->nullable(); // class icon, e.g., "fa fa-home"
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('menus');
    }
};