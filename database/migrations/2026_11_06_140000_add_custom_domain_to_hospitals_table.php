<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique()->after('slug');
            $table->string('custom_domain_status')->default('pending')->after('custom_domain');
            $table->timestamp('custom_domain_verified_at')->nullable()->after('custom_domain_status');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn([
                'custom_domain',
                'custom_domain_status',
                'custom_domain_verified_at',
            ]);
        });
    }
};
