<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Interactive API documentation for MediSlot – Connected Healthcare Management System & Real-Time Hospital Capacity Finder. Test all endpoints directly via this interface.',
    title: 'MediSlot HMS & Finder REST API v1',
    contact: new OA\Contact(name: 'MediSlot Support', email: 'support@medislot.example')
)]
#[OA\Server(url: '/', description: 'Default Server')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    bearerFormat: 'Bearer',
    scheme: 'bearer',
    description: 'Personal Access Token obtained from /api/v1/auth/login or /api/v1/auth/department-login'
)]
class OpenApiSpec
{
    // ==========================================
    // AUTHENTICATION
    // ==========================================

    #[OA\Post(path: '/api/v1/auth/login', summary: 'Login with Email & Password', tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'hannah@riverside.example'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Token and User profile returned')]
    #[OA\Response(response: 422, description: 'Invalid login credentials')]
    public function login(): void {}

    #[OA\Post(path: '/api/v1/auth/department-login', summary: 'Login with Department Username Handle', tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['department_username', 'password'],
        properties: [
            new OA\Property(property: 'department_username', type: 'string', example: 'reception/riverside'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Authenticated for department')]
    #[OA\Response(response: 422, description: 'Invalid department handle')]
    public function departmentLogin(): void {}

    #[OA\Post(path: '/api/v1/auth/register-hospital', summary: 'Onboard New Hospital & Admin Account', tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['hospital_name', 'state', 'lga', 'address', 'admin_name', 'admin_email', 'password'],
        properties: [
            new OA\Property(property: 'hospital_name', type: 'string', example: 'Apex Specialist Clinic'),
            new OA\Property(property: 'hospital_type', type: 'string', example: 'Specialist Hospital'),
            new OA\Property(property: 'state', type: 'string', example: 'Plateau'),
            new OA\Property(property: 'lga', type: 'string', example: 'Jos North'),
            new OA\Property(property: 'address', type: 'string', example: '12 Hospital Road'),
            new OA\Property(property: 'admin_name', type: 'string', example: 'Dr. John Doe'),
            new OA\Property(property: 'admin_email', type: 'string', format: 'email', example: 'admin@apex.example'),
            new OA\Property(property: 'password', type: 'string', example: 'password'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Hospital and Admin registered')]
    public function registerHospital(): void {}

    #[OA\Post(path: '/api/v1/auth/forgot-password', summary: 'Request Password Reset Token', tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['email'],
        properties: [
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@riverside.test'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Reset instructions dispatched / token generated')]
    #[OA\Response(response: 422, description: 'Validation failed')]
    public function forgotPassword(): void {}

    #[OA\Post(path: '/api/v1/auth/reset-password', summary: 'Reset Password with Token', tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['token', 'email', 'password', 'password_confirmation'],
        properties: [
            new OA\Property(property: 'token', type: 'string', example: 'abcd1234efgh5678'),
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@riverside.test'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'new-secure-password'),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'new-secure-password'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Password reset successfully')]
    #[OA\Response(response: 422, description: 'Invalid token or email')]
    public function resetPassword(): void {}

    #[OA\Get(path: '/api/v1/auth/me', summary: 'Get Current Authenticated User', security: [['sanctum' => []]], tags: ['Authentication'])]
    #[OA\Response(response: 200, description: 'Authenticated user profile')]
    public function me(): void {}

    #[OA\Post(path: '/api/v1/auth/change-password', summary: 'Change Password (Authenticated User/Department)', security: [['sanctum' => []]], tags: ['Authentication'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['current_password', 'password', 'password_confirmation'],
        properties: [
            new OA\Property(property: 'current_password', type: 'string', format: 'password', example: 'password'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'brand-new-password'),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'brand-new-password'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Password updated successfully')]
    #[OA\Response(response: 422, description: 'Validation or current password mismatch')]
    public function changePassword(): void {}

    #[OA\Post(path: '/api/v1/auth/logout', summary: 'Sign Out / Invalidate Token', security: [['sanctum' => []]], tags: ['Authentication'])]
    #[OA\Response(response: 200, description: 'Token revoked')]
    public function logout(): void {}

    // ==========================================
    // PUBLIC FINDER
    // ==========================================

    #[OA\Get(path: '/api/v1/finder/hospitals', summary: 'Search Hospitals and Real-time Bed Availability', tags: ['Public Finder'])]
    #[OA\Parameter(name: 'q', in: 'query', required: false, description: 'Hospital name or keyword', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'state', in: 'query', required: false, description: 'Filter by state', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'care_type', in: 'query', required: false, description: 'Accident / Trauma, Maternity / Delivery, General Emergency, Child Emergency', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Hospitals list with live capacity statistics')]
    public function finderHospitals(): void {}

    #[OA\Get(path: '/api/v1/finder/hospitals/{slugOrId}', summary: 'Get Hospital Public Details & Wards', tags: ['Public Finder'])]
    #[OA\Parameter(name: 'slugOrId', in: 'path', required: true, description: 'Hospital slug (e.g. riverside) or numeric ID', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Hospital profile, wards breakdown, and services')]
    public function finderHospitalDetail(): void {}

    // ==========================================
    // RECEPTION DESK
    // ==========================================

    #[OA\Get(path: '/api/v1/reception/dashboard', summary: 'Reception Desk Dashboard', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\Response(response: 200, description: 'Dashboard metrics and queue counters')]
    public function receptionDashboard(): void {}

    #[OA\Get(path: '/api/v1/reception/patients', summary: 'List Registered Patients', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\Parameter(name: 'q', in: 'query', required: false, description: 'Search query', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Patients directory')]
    public function receptionPatients(): void {}

    #[OA\Post(path: '/api/v1/reception/patients', summary: 'Register New Patient', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['first_name', 'last_name', 'gender'],
        properties: [
            new OA\Property(property: 'first_name', type: 'string', example: 'Amina'),
            new OA\Property(property: 'last_name', type: 'string', example: 'Yusuf'),
            new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1992-06-15'),
            new OA\Property(property: 'gender', type: 'string', enum: ['Female', 'Male', 'Other'], example: 'Female'),
            new OA\Property(property: 'phone', type: 'string', example: '0803 000 0124'),
            new OA\Property(property: 'address', type: 'string', example: '14 Marina Road, Jos'),
            new OA\Property(property: 'emergency_contact', type: 'string', example: 'Brother · 0802 000 0000'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Patient registered with MED-XXXX identifier')]
    public function receptionStorePatient(): void {}

    #[OA\Get(path: '/api/v1/reception/patients/{patient}', summary: 'Get Patient Record & History', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\Parameter(name: 'patient', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Patient details and encounter timeline')]
    public function receptionShowPatient(): void {}

    #[OA\Get(path: '/api/v1/reception/visits', summary: 'List Today Visits Queue', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['waiting', 'in_consultation', 'completed', 'cancelled']))]
    #[OA\Response(response: 200, description: 'Visits queue')]
    public function receptionVisits(): void {}

    #[OA\Post(path: '/api/v1/reception/visits', summary: 'Start New Patient Visit (Triage)', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['patient_id', 'department_id', 'visit_type', 'reason_for_visit', 'priority'],
        properties: [
            new OA\Property(property: 'patient_id', type: 'integer', example: 1),
            new OA\Property(property: 'department_id', type: 'integer', example: 1),
            new OA\Property(property: 'clinician_id', type: 'integer', nullable: true, example: 3),
            new OA\Property(property: 'visit_type', type: 'string', enum: ['Walk-in', 'Appointment', 'Emergency'], example: 'Walk-in'),
            new OA\Property(property: 'reason_for_visit', type: 'string', example: 'Fever and headaches'),
            new OA\Property(property: 'priority', type: 'string', enum: ['Routine', 'Urgent', 'Emergency'], example: 'Urgent'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Visit started')]
    public function receptionStoreVisit(): void {}

    #[OA\Put(path: '/api/v1/reception/visits/{visit}/cancel', summary: 'Cancel Visit with Reason', security: [['sanctum' => []]], tags: ['Reception Desk'])]
    #[OA\Parameter(name: 'visit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['reason'],
        properties: [new OA\Property(property: 'reason', type: 'string', example: 'Patient had to leave')]
    ))]
    #[OA\Response(response: 200, description: 'Visit cancelled')]
    public function receptionCancelVisit(): void {}

    // ==========================================
    // CONSULTANT / DOCTOR
    // ==========================================

    #[OA\Get(path: '/api/v1/consultant/dashboard', summary: 'Consultant Workload Dashboard', security: [['sanctum' => []]], tags: ['Consultant / Doctor'])]
    #[OA\Response(response: 200, description: 'Doctor workload metrics')]
    public function consultantDashboard(): void {}

    #[OA\Get(path: '/api/v1/consultant/workspace/{visit}', summary: 'Patient Clinical Workspace', security: [['sanctum' => []]], tags: ['Consultant / Doctor'])]
    #[OA\Parameter(name: 'visit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Complete patient clinical chart')]
    public function consultantWorkspace(): void {}

    #[OA\Post(path: '/api/v1/consultant/workspace/{visit}/notes', summary: 'Save or Finalize Consultation Notes (SOAP)', security: [['sanctum' => []]], tags: ['Consultant / Doctor'])]
    #[OA\Parameter(name: 'visit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['status'],
        properties: [
            new OA\Property(property: 'chief_complaint', type: 'string', example: 'Fever for 3 days'),
            new OA\Property(property: 'history_of_present_illness', type: 'string', example: 'Accompanied by chills'),
            new OA\Property(property: 'examination_findings', type: 'string', example: 'Febrile, non-icteric'),
            new OA\Property(property: 'assessment_diagnosis', type: 'string', example: 'Acute uncomplicated malaria'),
            new OA\Property(property: 'treatment_plan', type: 'string', example: 'ACT antimalarial'),
            new OA\Property(property: 'follow_up_instructions', type: 'string', example: 'Return in 5 days'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'finalized'], example: 'finalized'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Notes recorded')]
    public function consultantSaveNotes(): void {}

    #[OA\Post(path: '/api/v1/consultant/workspace/{visit}/lab-requests', summary: 'Order Laboratory Test', security: [['sanctum' => []]], tags: ['Consultant / Doctor'])]
    #[OA\Parameter(name: 'visit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['category', 'test_name', 'priority'],
        properties: [
            new OA\Property(property: 'category', type: 'string', example: 'Parasitology'),
            new OA\Property(property: 'test_name', type: 'string', example: 'Malaria Parasite Film'),
            new OA\Property(property: 'priority', type: 'string', enum: ['Routine', 'Urgent'], example: 'Urgent'),
            new OA\Property(property: 'clinical_reason', type: 'string', example: 'High fever'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Test ordered')]
    public function consultantStoreLabRequest(): void {}

    #[OA\Post(path: '/api/v1/consultant/workspace/{visit}/prescriptions', summary: 'Prescribe Medications', security: [['sanctum' => []]], tags: ['Consultant / Doctor'])]
    #[OA\Parameter(name: 'visit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['items'],
        properties: [
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                required: ['medication_name', 'dosage', 'frequency', 'duration', 'quantity_prescribed'],
                properties: [
                    new OA\Property(property: 'medication_name', type: 'string', example: 'Amoxicillin'),
                    new OA\Property(property: 'strength', type: 'string', example: '500mg'),
                    new OA\Property(property: 'dosage', type: 'string', example: '1 capsule'),
                    new OA\Property(property: 'frequency', type: 'string', example: 'TDS'),
                    new OA\Property(property: 'duration', type: 'string', example: '5 days'),
                    new OA\Property(property: 'quantity_prescribed', type: 'integer', example: 15),
                ]
            )),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Prescription sent to pharmacy')]
    public function consultantStorePrescription(): void {}

    // ==========================================
    // LABORATORY
    // ==========================================

    #[OA\Get(path: '/api/v1/lab/dashboard', summary: 'Laboratory Dashboard', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Response(response: 200, description: 'Specimen counts')]
    public function labDashboard(): void {}

    #[OA\Get(path: '/api/v1/lab/requests', summary: 'List Laboratory Requests', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Response(response: 200, description: 'Requests list')]
    public function labRequests(): void {}

    #[OA\Get(path: '/api/v1/lab/requests/{laboratoryRequest}', summary: 'Get Request Details', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Parameter(name: 'laboratoryRequest', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Request details')]
    public function labShowRequest(): void {}

    #[OA\Put(path: '/api/v1/lab/requests/{laboratoryRequest}/status', summary: 'Update Sample Stage in Pipeline', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Parameter(name: 'laboratoryRequest', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['status'],
        properties: [
            new OA\Property(property: 'status', type: 'string', enum: ['accepted', 'awaiting_sample', 'sample_collected', 'processing', 'result_entry', 'submitted'], example: 'sample_collected'),
            new OA\Property(property: 'sample_type', type: 'string', example: 'Venous Blood'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Status updated')]
    public function labUpdateStatus(): void {}

    #[OA\Post(path: '/api/v1/lab/requests/{laboratoryRequest}/results', summary: 'Submit Diagnostic Results', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Parameter(name: 'laboratoryRequest', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['result_value', 'status'],
        properties: [
            new OA\Property(property: 'result_value', type: 'string', example: 'Positive (++)'),
            new OA\Property(property: 'unit', type: 'string', example: '/uL'),
            new OA\Property(property: 'reference_range', type: 'string', example: 'Negative'),
            new OA\Property(property: 'interpretation', type: 'string', example: 'Moderate parasitemia'),
            new OA\Property(property: 'lab_notes', type: 'string', example: 'Bench analyzer 1'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'finalized'], example: 'finalized'),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Results recorded and released')]
    public function labEnterResults(): void {}

    #[OA\Get(path: '/api/v1/lab/reports', summary: 'Finalized Diagnostic Reports', security: [['sanctum' => []]], tags: ['Laboratory'])]
    #[OA\Response(response: 200, description: 'Reports list')]
    public function labReports(): void {}

    // ==========================================
    // PHARMACY
    // ==========================================

    #[OA\Get(path: '/api/v1/pharmacy/dashboard', summary: 'Pharmacy Dispensary Dashboard', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Response(response: 200, description: 'Pharmacy metrics')]
    public function pharmacyDashboard(): void {}

    #[OA\Get(path: '/api/v1/pharmacy/prescriptions', summary: 'Prescriptions Queue', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Response(response: 200, description: 'Prescriptions list')]
    public function pharmacyPrescriptions(): void {}

    #[OA\Get(path: '/api/v1/pharmacy/prescriptions/{prescription}', summary: 'Prescription Item Details', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Parameter(name: 'prescription', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Prescription details')]
    public function pharmacyShowPrescription(): void {}

    #[OA\Put(path: '/api/v1/pharmacy/prescriptions/{prescription}/accept', summary: 'Accept Prescription', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Parameter(name: 'prescription', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Prescription accepted')]
    public function pharmacyAccept(): void {}

    #[OA\Put(path: '/api/v1/pharmacy/prescriptions/{prescription}/begin', summary: 'Begin Dispensing Drug Order', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Parameter(name: 'prescription', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Prescription processing')]
    public function pharmacyBegin(): void {}

    #[OA\Put(path: '/api/v1/pharmacy/prescriptions/{prescription}/dispense', summary: 'Complete Dispensing & Deduct Inventory', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Parameter(name: 'prescription', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Medications dispensed and inventory deducted')]
    public function pharmacyDispense(): void {}

    #[OA\Post(path: '/api/v1/pharmacy/prescriptions/{prescription}/clarify', summary: 'Request Doctor Clarification', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Parameter(name: 'prescription', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['clarification_note'],
        properties: [new OA\Property(property: 'clarification_note', type: 'string', example: 'Allergy flagged')]
    ))]
    #[OA\Response(response: 200, description: 'Clarification sent to doctor')]
    public function pharmacyClarify(): void {}

    #[OA\Get(path: '/api/v1/pharmacy/inventory', summary: 'Medication Inventory List', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\Response(response: 200, description: 'Inventory stock on hand')]
    public function pharmacyInventory(): void {}

    #[OA\Post(path: '/api/v1/pharmacy/inventory', summary: 'Add Medication to Formulary', security: [['sanctum' => []]], tags: ['Pharmacy'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['name', 'form', 'unit_of_measurement', 'quantity', 'reorder_level'],
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Paracetamol'),
            new OA\Property(property: 'form', type: 'string', example: 'Tablet'),
            new OA\Property(property: 'unit_of_measurement', type: 'string', example: 'tablets'),
            new OA\Property(property: 'quantity', type: 'integer', example: 500),
            new OA\Property(property: 'reorder_level', type: 'integer', example: 50),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Medication added to inventory')]
    public function pharmacyStoreMedication(): void {}

    // ==========================================
    // HOSPITAL ADMIN
    // ==========================================

    #[OA\Get(path: '/api/v1/admin/dashboard', summary: 'Admin KPI Dashboard', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Hospital KPI metrics')]
    public function adminDashboard(): void {}

    #[OA\Get(path: '/api/v1/admin/departments', summary: 'Hospital Departments', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Departments list')]
    public function adminDepartments(): void {}

    #[OA\Post(path: '/api/v1/admin/departments', summary: 'Create Department', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['name', 'type'],
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Intensive Care Unit'),
            new OA\Property(property: 'type', type: 'string', example: 'clinical'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Department created')]
    public function adminStoreDepartment(): void {}

    #[OA\Put(path: '/api/v1/admin/departments/{department}/toggle', summary: 'Toggle Department Status', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Parameter(name: 'department', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Status toggled')]
    public function adminToggleDepartment(): void {}

    #[OA\Get(path: '/api/v1/admin/staff', summary: 'Hospital Staff Directory', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Staff directory')]
    public function adminStaff(): void {}

    #[OA\Post(path: '/api/v1/admin/staff', summary: 'Create Staff Member', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['name', 'email', 'role', 'password'],
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Nurse Mary Danjuma'),
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'mary@riverside.example'),
            new OA\Property(property: 'role', type: 'string', enum: ['hospital_admin', 'reception', 'consultant', 'lab_scientist', 'pharmacist'], example: 'reception'),
            new OA\Property(property: 'password', type: 'string', example: 'password'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Staff account created')]
    public function adminStoreStaff(): void {}

    #[OA\Get(path: '/api/v1/admin/beds', summary: 'Bed Categories & Capacities', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Bed occupancy')]
    public function adminBeds(): void {}

    #[OA\Put(path: '/api/v1/admin/beds/{bedCategory}', summary: 'Update Bed Capacity', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Parameter(name: 'bedCategory', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['total_beds', 'out_of_service_beds'],
        properties: [
            new OA\Property(property: 'total_beds', type: 'integer', example: 25),
            new OA\Property(property: 'out_of_service_beds', type: 'integer', example: 1),
        ]
    ))]
    #[OA\Response(response: 200, description: 'Beds updated')]
    public function adminUpdateBeds(): void {}

    #[OA\Get(path: '/api/v1/admin/admissions', summary: 'Active Inpatient Admissions', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Admissions list')]
    public function adminAdmissions(): void {}

    #[OA\Put(path: '/api/v1/admin/admissions/{admission}/discharge', summary: 'Discharge Inpatient', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Parameter(name: 'admission', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Patient discharged and bed vacated')]
    public function adminDischarge(): void {}

    #[OA\Get(path: '/api/v1/admin/services', summary: 'Hospital Services', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Services list')]
    public function adminServices(): void {}

    #[OA\Get(path: '/api/v1/admin/settings', summary: 'Facility Settings', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Settings')]
    public function adminSettings(): void {}

    #[OA\Get(path: '/api/v1/admin/activity', summary: 'Hospital Audit Log', security: [['sanctum' => []]], tags: ['Hospital Admin'])]
    #[OA\Response(response: 200, description: 'Audit trail')]
    public function adminActivity(): void {}

    // ==========================================
    // MESSAGES
    // ==========================================

    #[OA\Get(path: '/api/v1/messages', summary: 'Inter-department Communication Threads', security: [['sanctum' => []]], tags: ['Messages'])]
    #[OA\Response(response: 200, description: 'Messages list')]
    public function messagesIndex(): void {}

    #[OA\Post(path: '/api/v1/messages', summary: 'Send Inter-department Message', security: [['sanctum' => []]], tags: ['Messages'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['messageable_type', 'messageable_id', 'message'],
        properties: [
            new OA\Property(property: 'messageable_type', type: 'string', example: 'App\\Models\\Prescription'),
            new OA\Property(property: 'messageable_id', type: 'integer', example: 1),
            new OA\Property(property: 'message', type: 'string', example: 'Dose verified'),
        ]
    ))]
    #[OA\Response(response: 201, description: 'Message delivered')]
    public function messagesStore(): void {}
}
