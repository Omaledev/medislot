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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('General Hospital');
            $table->string('email');
            $table->string('phone');
            $table->string('country')->default('Nigeria');
            $table->string('state');
            $table->string('lga');
            $table->text('address');
            $table->string('registration_number')->nullable();
            $table->string('website')->nullable();
            $table->enum('emergency_status', ['accepting', 'limited', 'not_accepting'])->default('accepting');
            $table->string('location_for_directions')->nullable();
            $table->integer('warn_unupdated_hours')->default(1);
            $table->integer('mark_stale_hours')->default(3);
            $table->enum('status', ['active', 'pending_verification', 'inactive'])->default('active');
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ['clinical', 'diagnostic', 'support', 'administrative'])->default('clinical');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('lead_user_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('hospital_id')->nullable()->after('id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->after('hospital_id')->constrained('departments')->nullOnDelete();
            $table->enum('role', ['hospital_admin', 'reception', 'consultant', 'lab_scientist', 'pharmacist'])->default('hospital_admin')->after('password');
            $table->string('department_username')->nullable()->unique()->after('role');
            $table->string('phone')->nullable()->after('department_username');
            $table->string('title')->nullable()->after('phone');
            $table->enum('status', ['active', 'invited', 'deactivated'])->default('active')->after('title');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('lead_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('bed_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->integer('total_beds')->default(0);
            $table->integer('occupied_beds')->default(0);
            $table->integer('out_of_service_beds')->default(0);
            $table->integer('available_beds')->default(0);
            $table->enum('status', ['available', 'limited', 'full'])->default('available');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hospital_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->enum('status', ['available', 'limited', 'unavailable', 'unknown'])->default('available');
            $table->boolean('is_emergency_capable')->default(false);
            $table->boolean('is_public_visible')->default(true);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('patient_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['Female', 'Male', 'Other'])->default('Female');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('current_medications')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('clinician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visit_number');
            $table->string('visit_type')->default('Walk-in');
            $table->text('reason_for_visit');
            $table->enum('priority', ['Routine', 'Urgent', 'Emergency'])->default('Routine');
            $table->enum('status', ['waiting', 'in_consultation', 'completed', 'cancelled'])->default('waiting');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('arrived_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('consultation_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('clinician_id')->constrained('users')->cascadeOnDelete();
            $table->text('chief_complaint')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->text('relevant_medical_history')->nullable();
            $table->text('examination_findings')->nullable();
            $table->text('assessment_diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('laboratory_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('request_number');
            $table->string('category')->default('General');
            $table->string('test_name');
            $table->text('clinical_reason')->nullable();
            $table->enum('priority', ['Routine', 'Urgent'])->default('Routine');
            $table->text('instructions')->nullable();
            $table->enum('status', ['new', 'awaiting_sample', 'sample_collected', 'processing', 'result_entry', 'submitted'])->default('new');
            $table->string('sample_type')->nullable();
            $table->timestamp('sample_collected_at')->nullable();
            $table->foreignId('sample_collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('laboratory_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_request_id')->constrained('laboratory_requests')->cascadeOnDelete();
            $table->foreignId('entered_by')->constrained('users')->cascadeOnDelete();
            $table->text('result_value');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->text('interpretation')->nullable();
            $table->text('lab_notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['draft', 'finalized'])->default('finalized');
            $table->timestamp('finalized_at')->nullable();
            $table->text('clinical_notes_by_doctor')->nullable();
            $table->timestamps();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->default('Tablet');
            $table->string('unit_of_measurement')->default('Tablet');
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->enum('status', ['available', 'low_stock', 'out_of_stock', 'expired'])->default('available');
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('prescriber_id')->constrained('users')->cascadeOnDelete();
            $table->string('rx_number');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'new', 'accepted', 'processing', 'partially_dispensed', 'dispensed', 'clarification_needed', 'cancelled'])->default('new');
            $table->text('clarification_note')->nullable();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dispensed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('medication_id')->nullable()->constrained('medications')->nullOnDelete();
            $table->string('medication_name');
            $table->string('strength')->nullable();
            $table->string('dosage');
            $table->string('route')->default('Oral');
            $table->string('frequency')->default('Three times daily');
            $table->string('duration')->default('3 days');
            $table->integer('quantity_prescribed')->default(1);
            $table->integer('quantity_dispensed')->default(0);
            $table->text('instructions')->nullable();
            $table->enum('status', ['pending', 'dispensed', 'unavailable'])->default('pending');
            $table->timestamps();
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('bed_category_id')->constrained('bed_categories')->cascadeOnDelete();
            $table->string('admission_number');
            $table->timestamp('admitted_at')->useCurrent();
            $table->timestamp('discharged_at')->nullable();
            $table->enum('status', ['admitted', 'critical_care', 'discharged'])->default('admitted');
            $table->text('notes')->nullable();
            $table->foreignId('admitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('discharged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('messageable_type');
            $table->unsignedBigInteger('messageable_id');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['messageable_type', 'messageable_id']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('request_messages');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('laboratory_results');
        Schema::dropIfExists('laboratory_requests');
        Schema::dropIfExists('consultation_notes');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('hospital_services');
        Schema::dropIfExists('bed_categories');

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['lead_user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['hospital_id', 'department_id', 'role', 'department_username', 'phone', 'title', 'status']);
        });

        Schema::dropIfExists('departments');
        Schema::dropIfExists('hospitals');
    }
};
