<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('name');
            $table->string('key_prefix', 16);
            $table->string('key_hash', 64)->unique();
            $table->json('permissions')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::table('bed_categories', function (Blueprint $table) {
            $table->string('external_code')->nullable()->index()->after('name');
            $table->timestamp('last_synced_at')->nullable()->after('last_updated_at');
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->string('external_admission_id')->nullable()->index()->after('admission_number');
            $table->string('external_patient_id')->nullable()->index()->after('external_admission_id');
            $table->string('source')->default('internal')->after('status');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('integration_mode')->default('standalone')->after('status');
            $table->string('webhook_secret')->nullable()->after('integration_mode');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['integration_mode', 'webhook_secret']);
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn(['external_admission_id', 'external_patient_id', 'source']);
        });

        Schema::table('bed_categories', function (Blueprint $table) {
            $table->dropColumn(['external_code', 'last_synced_at']);
        });

        Schema::dropIfExists('hospital_api_keys');
    }
};
