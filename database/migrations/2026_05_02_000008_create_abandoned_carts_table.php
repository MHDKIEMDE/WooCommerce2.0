<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('items');              // snapshot des articles au moment de l'abandon
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('last_activity_at');
            $table->timestamp('notified_at')->nullable(); // null = pas encore notifié
            $table->timestamps();

            $table->unique('user_id'); // un seul panier abandonné par user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
