<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bed_categories', function (Blueprint $table) {
            $table->string('care_category')->default('general')->after('department_id');
            $table->boolean('is_emergency_intake')->default(false)->after('care_category');
            $table->string('restriction_scope')->nullable()->after('is_emergency_intake');
            $table->string('restriction_label')->nullable()->after('restriction_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bed_categories', function (Blueprint $table) {
            $table->dropColumn([
                'care_category',
                'is_emergency_intake',
                'restriction_scope',
                'restriction_label',
            ]);
        });
    }
};
