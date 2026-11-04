<?php

use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ConsultantWebController;
use App\Http\Controllers\Web\LabWebController;
use App\Http\Controllers\Web\PharmacyWebController;
use App\Http\Controllers\Web\PublicController;
use App\Http\Controllers\Web\ReceptionWebController;
use Illuminate\Support\Facades\Route;

// Public & Finder
Route::get('/', [PublicController::class, 'landing'])->name('home');
Route::get('/finder', [PublicController::class, 'finder'])->name('finder');
Route::get('/finder/results', [PublicController::class, 'finderResults'])->name('finder.results');
Route::get('/finder/hospitals/{hospital:slug}', [PublicController::class, 'finderHospital'])->name('finder.hospital');

// Authentication & Registration
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/department/login', [AuthController::class, 'showDepartmentLogin'])->name('department.login');
Route::post('/department/login', [AuthController::class, 'departmentLogin']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterStep1'])->name('register');
Route::post('/register/step-1', [AuthController::class, 'processRegisterStep1'])->name('register.step1');
Route::get('/register/administrator', [AuthController::class, 'showRegisterStep2'])->name('register.administrator');
Route::post('/register/step-2', [AuthController::class, 'processRegisterStep2'])->name('register.step2');
Route::get('/register/review', [AuthController::class, 'showRegisterReview'])->name('register.review');
Route::post('/register/complete', [AuthController::class, 'completeRegistration'])->name('register.complete');
Route::get('/register/ready', [AuthController::class, 'showAccountReady'])->name('register.ready');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'processForgotPassword']);
Route::get('/forgot-password/sent', [AuthController::class, 'showResetSent'])->name('forgot-password.sent');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'processResetPassword'])->name('password.update');

Route::get('/department/change-password', [AuthController::class, 'showChangePassword'])->name('department.change-password')->middleware('auth');
Route::post('/department/change-password', [AuthController::class, 'processChangePassword'])->name('department.change-password.post')->middleware('auth');

Route::redirect('/api/documentation', '/api/v1/documentation');
Route::redirect('/docs', '/api/v1/docs');

// Protected Portals
Route::middleware('auth')->group(function () {

    // Admin Portal
    Route::middleware('role:hospital_admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/departments', [AdminWebController::class, 'departments'])->name('departments');
        Route::post('/departments', [AdminWebController::class, 'storeDepartment'])->name('departments.store');
        Route::put('/departments/{department}/toggle', [AdminWebController::class, 'toggleDepartment'])->name('departments.toggle');
        Route::get('/staff', [AdminWebController::class, 'staff'])->name('staff');
        Route::post('/staff', [AdminWebController::class, 'storeStaff'])->name('staff.store');
        Route::get('/beds', [AdminWebController::class, 'beds'])->name('beds');
        Route::put('/beds/{bedCategory}', [AdminWebController::class, 'updateBedCapacity'])->name('beds.update');
        Route::post('/beds', [AdminWebController::class, 'storeBedCategory'])->name('beds.store');
        Route::get('/admissions', [AdminWebController::class, 'admissions'])->name('admissions');
        Route::post('/admissions', [AdminWebController::class, 'admitPatient'])->name('admissions.store');
        Route::put('/admissions/{admission}/discharge', [AdminWebController::class, 'dischargeAdmission'])->name('admissions.discharge');
        Route::get('/services', [AdminWebController::class, 'services'])->name('services');
        Route::post('/services', [AdminWebController::class, 'storeService'])->name('services.store');
        Route::put('/services/{service}', [AdminWebController::class, 'updateService'])->name('services.update');
        Route::get('/settings', [AdminWebController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminWebController::class, 'updateSettings'])->name('settings.update');
        Route::get('/activity', [AdminWebController::class, 'activity'])->name('activity');
    });

    // Reception Desk Portal
    Route::middleware('role:reception,hospital_admin')->prefix('reception')->as('reception.')->group(function () {
        Route::get('/dashboard', [ReceptionWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/patients', [ReceptionWebController::class, 'patients'])->name('patients');
        Route::get('/patients/create', [ReceptionWebController::class, 'createPatient'])->name('patients.create');
        Route::post('/patients', [ReceptionWebController::class, 'storePatient'])->name('patients.store');
        Route::get('/patients/{patient}', [ReceptionWebController::class, 'showPatient'])->name('patients.show');
        Route::get('/queue', [ReceptionWebController::class, 'queue'])->name('queue');
        Route::post('/visits/{visit}/cancel', [ReceptionWebController::class, 'cancelVisit'])->name('visits.cancel');
        Route::get('/visits/create', [ReceptionWebController::class, 'createVisit'])->name('visits.create');
        Route::post('/visits', [ReceptionWebController::class, 'storeVisit'])->name('visits.store');
        Route::get('/beds', [ReceptionWebController::class, 'beds'])->name('beds');
    });

    // Consultant (Doctor) Portal
    Route::middleware('role:consultant,hospital_admin')->prefix('consultant')->as('consultant.')->group(function () {
        Route::get('/dashboard', [ConsultantWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/patients', [ConsultantWebController::class, 'patients'])->name('patients');
        Route::get('/workspace/{visit}', [ConsultantWebController::class, 'workspace'])->name('workspace');
        Route::post('/workspace/{visit}/notes', [ConsultantWebController::class, 'saveNotes'])->name('workspace.notes');
        Route::post('/workspace/{visit}/prescriptions', [ConsultantWebController::class, 'createPrescription'])->name('workspace.prescriptions');
        Route::post('/workspace/{visit}/lab-requests', [ConsultantWebController::class, 'createLabRequest'])->name('workspace.lab-requests');
        Route::get('/history', [ConsultantWebController::class, 'history'])->name('history');
        Route::get('/lab-requests', [ConsultantWebController::class, 'labRequests'])->name('lab-requests');
        Route::get('/prescriptions', [ConsultantWebController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/messages', [ConsultantWebController::class, 'messages'])->name('messages');
        Route::post('/messages', [ConsultantWebController::class, 'sendMessage'])->name('messages.send');
        Route::get('/profile', [ConsultantWebController::class, 'profile'])->name('profile');
    });

    // Laboratory Portal
    Route::middleware('role:lab_scientist,hospital_admin')->prefix('lab')->as('lab.')->group(function () {
        Route::get('/dashboard', [LabWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/requests', [LabWebController::class, 'requests'])->name('requests');
        Route::get('/requests/{laboratoryRequest}', [LabWebController::class, 'showRequest'])->name('requests.show');
        Route::post('/requests/{laboratoryRequest}/results', [LabWebController::class, 'saveResult'])->name('requests.results');
        Route::get('/samples', [LabWebController::class, 'samples'])->name('samples');
        Route::post('/requests/{laboratoryRequest}/advance', [LabWebController::class, 'advanceSampleStatus'])->name('requests.advance');
        Route::get('/reports', [LabWebController::class, 'reports'])->name('reports');
        Route::get('/messages', [LabWebController::class, 'messages'])->name('messages');
        Route::post('/messages', [LabWebController::class, 'sendMessage'])->name('messages.send');
        Route::get('/profile', [LabWebController::class, 'profile'])->name('profile');
    });

    // Pharmacy Portal
    Route::middleware('role:pharmacist,hospital_admin')->prefix('pharmacy')->as('pharmacy.')->group(function () {
        Route::get('/dashboard', [PharmacyWebController::class, 'dashboard'])->name('dashboard');
        Route::get('/prescriptions', [PharmacyWebController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}', [PharmacyWebController::class, 'showPrescription'])->name('prescriptions.show');
        Route::post('/prescriptions/{prescription}/accept', [PharmacyWebController::class, 'acceptPrescription'])->name('prescriptions.accept');
        Route::post('/prescriptions/{prescription}/begin', [PharmacyWebController::class, 'beginDispensing'])->name('prescriptions.begin');
        Route::post('/prescriptions/{prescription}/dispense', [PharmacyWebController::class, 'dispensePrescription'])->name('prescriptions.dispense');
        Route::post('/prescriptions/{prescription}/clarify', [PharmacyWebController::class, 'requestClarification'])->name('prescriptions.clarify');
        Route::get('/dispensing', [PharmacyWebController::class, 'dispensingQueue'])->name('dispensing');
        Route::get('/dispensing-history', [PharmacyWebController::class, 'dispensingHistory'])->name('dispensing-history');
        Route::get('/inventory', [PharmacyWebController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/create', [PharmacyWebController::class, 'createMedication'])->name('inventory.create');
        Route::post('/inventory', [PharmacyWebController::class, 'storeMedication'])->name('inventory.store');
        Route::get('/messages', [PharmacyWebController::class, 'messages'])->name('messages');
        Route::post('/messages', [PharmacyWebController::class, 'sendMessage'])->name('messages.send');
        Route::get('/profile', [PharmacyWebController::class, 'profile'])->name('profile');
    });
});
