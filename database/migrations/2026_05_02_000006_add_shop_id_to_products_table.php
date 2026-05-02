<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // nullable pour compatibilité avec les produits Agri-Shop existants (boutique #1)
            $table->foreignId('shop_id')->nullable()->after('id')
                  ->constrained('shops')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Shop::class);
            $table->dropColumn('shop_id');
        });
    }
};
