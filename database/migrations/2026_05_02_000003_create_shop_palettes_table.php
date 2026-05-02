<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_palettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('shop_templates')->cascadeOnDelete();
            $table->string('name');
            $table->string('color_primary', 7);
            $table->string('color_accent', 7);
            $table->string('color_bg', 7);
            $table->string('color_text', 7)->default('#1a1a1a');
            $table->string('ambiance')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_palettes');
    }
};
