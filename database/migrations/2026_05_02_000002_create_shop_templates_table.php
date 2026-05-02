<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // mode, food, digital, craft, tech, beauty, generic
            $table->string('icon', 10)->nullable();
            $table->json('sections')->nullable();  // sections disponibles pour ce template
            $table->json('fonts')->nullable();      // polices proposées
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_templates');
    }
};
