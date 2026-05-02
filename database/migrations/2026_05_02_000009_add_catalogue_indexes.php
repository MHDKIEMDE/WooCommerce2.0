<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'featured']);
            $table->index(['shop_id', 'status']);
            $table->index('rating_avg');
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->index(['status', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status', 'featured']);
            $table->dropIndex(['shop_id', 'status']);
            $table->dropIndex(['rating_avg']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropIndex(['status', 'template_id']);
        });
    }
};
