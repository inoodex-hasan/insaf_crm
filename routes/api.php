<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\PartnerCommissionApiController;
use App\Http\Controllers\Api\v1\PartnerAgencyApiController;
use App\Http\Controllers\Api\v1\ExpenseApiController;
use App\Http\Controllers\Api\v1\OfficeAccountApiController;
use App\Http\Controllers\Api\v1\StudentApiController;
use App\Http\Controllers\Api\v1\ApplicationApiController;
use App\Http\Controllers\Api\v1\LeadApiController;
use App\Http\Controllers\Api\v1\AttendanceApiController;

Route::prefix('v1')->group(function () {
    // Public Authentication
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // Protected Routes (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Authenticated User & Session
        Route::prefix('auth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::post('logout', [AuthController::class, 'logout']);
        });

        // Partner Commissions (B2B University & Agency)
        Route::prefix('partner-commissions')->group(function () {
            Route::get('/', [PartnerCommissionApiController::class, 'index']);
            Route::post('/', [PartnerCommissionApiController::class, 'store']);
            Route::get('{partnerCommission}', [PartnerCommissionApiController::class, 'show']);
            Route::put('{partnerCommission}', [PartnerCommissionApiController::class, 'update']);
            Route::delete('{partnerCommission}', [PartnerCommissionApiController::class, 'destroy']);
            Route::post('{partnerCommission}/settle', [PartnerCommissionApiController::class, 'settle']);
        });

        // Partner Agencies Directory
        Route::prefix('partner-agencies')->group(function () {
            Route::get('/', [PartnerAgencyApiController::class, 'index']);
            Route::post('/', [PartnerAgencyApiController::class, 'store']);
            Route::get('{partnerAgency}', [PartnerAgencyApiController::class, 'show']);
            Route::put('{partnerAgency}', [PartnerAgencyApiController::class, 'update']);
            Route::delete('{partnerAgency}', [PartnerAgencyApiController::class, 'destroy']);
        });

        // Expenses Management
        Route::prefix('expenses')->group(function () {
            Route::get('/', [ExpenseApiController::class, 'index']);
            Route::post('/', [ExpenseApiController::class, 'store']);
            Route::get('{expense}', [ExpenseApiController::class, 'show']);
            Route::put('{expense}', [ExpenseApiController::class, 'update']);
            Route::delete('{expense}', [ExpenseApiController::class, 'destroy']);
        });

        // Office Accounts
        Route::prefix('office-accounts')->group(function () {
            Route::get('/', [OfficeAccountApiController::class, 'index']);
            Route::post('/', [OfficeAccountApiController::class, 'store']);
            Route::get('{officeAccount}', [OfficeAccountApiController::class, 'show']);
            Route::put('{officeAccount}', [OfficeAccountApiController::class, 'update']);
            Route::delete('{officeAccount}', [OfficeAccountApiController::class, 'destroy']);
        });

        // Students Management & Dossier
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentApiController::class, 'index']);
            Route::post('/', [StudentApiController::class, 'store']);
            Route::get('country-remarks', [StudentApiController::class, 'countryRemarks']);
            Route::get('{student}', [StudentApiController::class, 'show']);
            Route::put('{student}', [StudentApiController::class, 'update']);
            Route::patch('{student}/quick-update', [StudentApiController::class, 'quickUpdate']);
            Route::delete('{student}', [StudentApiController::class, 'destroy']);
        });

        // Student Applications
        Route::prefix('applications')->group(function () {
            Route::get('/', [ApplicationApiController::class, 'index']);
            Route::post('/', [ApplicationApiController::class, 'store']);
            Route::get('{application}', [ApplicationApiController::class, 'show']);
            Route::put('{application}', [ApplicationApiController::class, 'update']);
            Route::delete('{application}', [ApplicationApiController::class, 'destroy']);
            Route::get('{application}/invoice', [ApplicationApiController::class, 'invoiceData']);
        });

        // Leads & Marketing Acquisition
        Route::prefix('leads')->group(function () {
            Route::get('/', [LeadApiController::class, 'index']);
            Route::post('/', [LeadApiController::class, 'store']);
            Route::get('{lead}', [LeadApiController::class, 'show']);
            Route::put('{lead}', [LeadApiController::class, 'update']);
            Route::delete('{lead}', [LeadApiController::class, 'destroy']);
        });

        // Staff Attendance & HR
        Route::prefix('attendance')->group(function () {
            Route::post('check-in', [AttendanceApiController::class, 'checkIn']);
            Route::post('check-out', [AttendanceApiController::class, 'checkOut']);
            Route::get('my', [AttendanceApiController::class, 'myAttendance']);
            Route::get('settings', [AttendanceApiController::class, 'settings']);
            Route::get('report', [AttendanceApiController::class, 'report']);
            Route::get('/', [AttendanceApiController::class, 'index']);
        });
    });
});
