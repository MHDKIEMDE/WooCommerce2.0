<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir les rôles existants avant de modifier l'enum
        DB::table('users')->where('role', 'customer')->update(['role' => 'buyer']);
        DB::table('users')->where('role', 'super-admin')->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer')->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'buyer')->update(['role' => 'customer']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'admin', 'super-admin'])->default('customer')->change();
        });
    }
};
