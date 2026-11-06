<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConsultantController;
use App\Http\Controllers\Api\V1\FinderController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\LaboratoryController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\PharmacyController;
use App\Http\Controllers\Api\V1\ReceptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication & Registration OTP
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/department-login', [AuthController::class, 'departmentLogin']);
    Route::post('/auth/send-registration-otp', [AuthController::class, 'sendRegistrationOtp']);
    Route::post('/auth/verify-registration-otp', [AuthController::class, 'verifyRegistrationOtp']);
    Route::post('/auth/resend-registration-otp', [AuthController::class, 'resendRegistrationOtp']);
    Route::post('/auth/register-hospital', [AuthController::class, 'registerHospital']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Public Finder API
    Route::get('/finder/hospitals', [FinderController::class, 'index']);
    Route::get('/finder/hospitals/{slugOrId}', [FinderController::class, 'show']);
    Route::post('/public/contact', [FinderController::class, 'contact']);

    // Authenticated Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Admin
        Route::prefix('admin')->middleware('role:hospital_admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/departments', [AdminController::class, 'departments']);
            Route::post('/departments', [AdminController::class, 'storeDepartment']);
            Route::put('/departments/{department}/toggle', [AdminController::class, 'toggleDepartmentStatus']);
            Route::get('/staff', [AdminController::class, 'staff']);
            Route::post('/staff', [AdminController::class, 'storeStaff']);
            Route::get('/beds', [AdminController::class, 'beds']);
            Route::put('/beds/{bedCategory}', [AdminController::class, 'updateBedCapacity']);
            Route::get('/admissions', [AdminController::class, 'admissions']);
            Route::put('/admissions/{admission}/discharge', [AdminController::class, 'dischargeAdmission']);
            Route::get('/services', [AdminController::class, 'services']);
            Route::put('/services/{service}', [AdminController::class, 'updateService']);
            Route::get('/settings', [AdminController::class, 'settings']);
            Route::put('/settings', [AdminController::class, 'updateSettings']);
            Route::get('/activity', [AdminController::class, 'activity']);
        });

        // Reception
        Route::prefix('reception')->middleware('role:reception,hospital_admin')->group(function () {
            Route::get('/dashboard', [ReceptionController::class, 'dashboard']);
            Route::get('/patients', [ReceptionController::class, 'patients']);
            Route::post('/patients', [ReceptionController::class, 'storePatient']);
            Route::get('/patients/{patient}', [ReceptionController::class, 'showPatient']);
            Route::match(['put', 'patch'], '/patients/{patient}', [ReceptionController::class, 'updatePatient']);
            Route::get('/visits', [ReceptionController::class, 'visits']);
            Route::post('/visits', [ReceptionController::class, 'storeVisit']);
            Route::match(['put', 'patch'], '/visits/{visit}', [ReceptionController::class, 'updateVisit']);
            Route::put('/visits/{visit}/cancel', [ReceptionController::class, 'cancelVisit']);
            Route::put('/settings', [ReceptionController::class, 'updateSettings']);
        });

        // Consultant
        Route::prefix('consultant')->middleware('role:consultant,hospital_admin')->group(function () {
            Route::get('/dashboard', [ConsultantController::class, 'dashboard']);
            Route::get('/workspace/{visit}', [ConsultantController::class, 'workspace']);
            Route::post('/workspace/{visit}/notes', [ConsultantController::class, 'saveNotes']);
            Route::post('/workspace/{visit}/prescriptions', [ConsultantController::class, 'storePrescription']);
            Route::post('/workspace/{visit}/lab-requests', [ConsultantController::class, 'storeLabRequest']);
            Route::put('/profile', [ConsultantController::class, 'updateProfile']);
        });

        // Laboratory
        Route::prefix('lab')->middleware('role:lab_scientist,laboratory,hospital_admin')->group(function () {
            Route::get('/dashboard', [LaboratoryController::class, 'dashboard']);
            Route::get('/requests', [LaboratoryController::class, 'requests']);
            Route::get('/requests/{laboratoryRequest}', [LaboratoryController::class, 'show']);
            Route::match(['put', 'patch'], '/requests/{laboratoryRequest}', [LaboratoryController::class, 'updateRequest']);
            Route::put('/requests/{laboratoryRequest}/status', [LaboratoryController::class, 'updateSampleStatus']);
            Route::post('/requests/{laboratoryRequest}/results', [LaboratoryController::class, 'enterResult']);
            Route::post('/requests/{laboratoryRequest}/amend', [LaboratoryController::class, 'amend']);
            Route::post('/requests/{laboratoryRequest}/attachments', [LaboratoryController::class, 'uploadAttachment']);
            Route::get('/reports', [LaboratoryController::class, 'reports']);
            Route::put('/profile', [LaboratoryController::class, 'updateProfile']);
        });

        // Pharmacy
        Route::prefix('pharmacy')->middleware('role:pharmacist,pharmacy,hospital_admin')->group(function () {
            Route::get('/dashboard', [PharmacyController::class, 'dashboard']);
            Route::get('/prescriptions', [PharmacyController::class, 'prescriptions']);
            Route::get('/prescriptions/{prescription}', [PharmacyController::class, 'showPrescription']);
            Route::put('/prescriptions/{prescription}/accept', [PharmacyController::class, 'acceptPrescription']);
            Route::put('/prescriptions/{prescription}/begin', [PharmacyController::class, 'beginDispensing']);
            Route::put('/prescriptions/{prescription}/dispense', [PharmacyController::class, 'dispensePrescription']);
            Route::post('/prescriptions/{prescription}/clarify', [PharmacyController::class, 'requestClarification']);
            Route::get('/inventory', [PharmacyController::class, 'inventory']);
            Route::post('/inventory', [PharmacyController::class, 'storeMedication']);
            Route::get('/inventory/{medication}', [PharmacyController::class, 'showMedication']);
            Route::post('/inventory/{medication}/receive-stock', [PharmacyController::class, 'receiveStock']);
            Route::post('/inventory/{medication}/adjust-stock', [PharmacyController::class, 'adjustStock']);
            Route::put('/profile', [PharmacyController::class, 'updateProfile']);
        });

        // Messages
        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
    });

    // External Hospital Integrations API (MediSlot Connect)
    Route::prefix('integrations')->middleware('hospital.api_key')->group(function () {
        Route::get('/status', [IntegrationController::class, 'status']);
        Route::post('/adt/admit', [IntegrationController::class, 'admit']);
        Route::post('/adt/discharge', [IntegrationController::class, 'discharge']);
        Route::post('/adt/transfer', [IntegrationController::class, 'transfer']);
        Route::put('/beds/sync', [IntegrationController::class, 'syncBeds']);
        Route::post('/beds/allocate', [IntegrationController::class, 'allocateBed']);
    });
});
