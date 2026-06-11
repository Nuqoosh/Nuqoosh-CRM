<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add address fields to companies table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('companies', 'country')) {
                $table->string('country')->nullable()->after('address');
            }
        });
        
        // Add address and phone to clients (if missing)
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('clients', 'phone')) {
                $table->string('phone')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['address', 'country']);
        });
        
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone']);
        });
    }
};