<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('document_templates', 'category')) {
                $table->string('category')->nullable()->after('type');
            }
            if (!Schema::hasColumn('document_templates', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            if (Schema::hasColumn('document_templates', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('document_templates', 'sub_category')) {
                $table->dropColumn('sub_category');
            }
        });
    }
};