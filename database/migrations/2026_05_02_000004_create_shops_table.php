<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('shop_templates');
            $table->foreignId('palette_id')->constrained('shop_palettes');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique()->nullable(); // ex: monghetto-shop
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('font')->nullable();
            $table->enum('layout', ['2', '3', '4', 'list'])->default('3');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->string('stripe_account_id')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
