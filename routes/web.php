<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Admin\{ApplicationController, CommissionController, CountryController, CourseController, CourseIntakeController, CurrencyController, DashboardController, LeadController, PaymentController, RoleController as LocalRoleController, SettingController, StudentController, UniversityController};

// Rate limiter for login routes (5 attempts per minute per IP)
RateLimiter::for('login', function () {
    return Limit::perMinute(5)->by(request()->ip());
});

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('tyro-dashboard.index');

Route::prefix('dashboard/settings')->name('admin.settings.')->middleware('can:manage-settings')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/update', [SettingController::class, 'update'])->name('update');
});

// Notifications
Route::prefix('dashboard/notifications')->name('admin.notifications.')->group(function () {
    Route::get('count', [App\Http\Controllers\Admin\NotificationController::class, 'getUnreadCount'])->name('count');
    Route::get('{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
    Route::get('read-all', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('readAll');
});

// Country Management
Route::prefix('dashboard/countries')->name('admin.countries.')->middleware('can:*editor')->group(function () {
    Route::get('/', [CountryController::class, 'index'])->name('index');
    Route::get('/create', [CountryController::class, 'create'])->name('create');
    Route::post('/', [CountryController::class, 'store'])->name('store');
    Route::get('{country}/edit', [CountryController::class, 'edit'])->name('edit');
    Route::put('{country}', [CountryController::class, 'update'])->name('update');
    Route::delete('{country}', [CountryController::class, 'destroy'])->name('destroy');
});

// University Management
Route::prefix('dashboard/universities')->name('admin.universities.')->middleware('can:*editor')->group(function () {
    Route::get('/', [UniversityController::class, 'index'])->name('index');
    Route::get('/create', [UniversityController::class, 'create'])->name('create');
    Route::post('/', [UniversityController::class, 'store'])->name('store');
    Route::get('{university}/edit', [UniversityController::class, 'edit'])->name('edit');
    Route::put('{university}', [UniversityController::class, 'update'])->name('update');
    Route::delete('{university}', [UniversityController::class, 'destroy'])->name('destroy');
});

// Course Management
Route::prefix('dashboard/courses')->name('admin.courses.')->middleware('can:*editor')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/create', [CourseController::class, 'create'])->name('create');
    Route::post('/', [CourseController::class, 'store'])->name('store');
    Route::get('{course}', [CourseController::class, 'show'])->name('show');
    Route::get('{course}/edit', [CourseController::class, 'edit'])->name('edit');
    Route::put('{course}', [CourseController::class, 'update'])->name('update');
    Route::delete('{course}', [CourseController::class, 'destroy'])->name('destroy');
});

// CourseIntake Management
// Route::prefix('dashboard/course-intakes')->name('admin.course-intakes.')->middleware('can:*editor')->group(function () {
//     Route::get('/', [CourseIntakeController::class, 'index'])->name('index');
//     Route::get('/create', [CourseIntakeController::class, 'create'])->name('create');
//     Route::post('/', [CourseIntakeController::class, 'store'])->name('store');
//     Route::get('{courseIntake}/edit', [CourseIntakeController::class, 'edit'])->name('edit');
//     Route::put('{courseIntake}', [CourseIntakeController::class, 'update'])->name('update');
//     Route::delete('{courseIntake}', [CourseIntakeController::class, 'destroy'])->name('destroy');
// });

// Student Management
Route::prefix('dashboard/students')->name('admin.students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index')->middleware('can:*consultant|*application|*marketing');
    Route::get('/create', [StudentController::class, 'create'])->name('create')->middleware('can:*consultant|*marketing');
    Route::post('/', [StudentController::class, 'store'])->name('store')->middleware('can:*consultant|*marketing');
    Route::get('{student}', [StudentController::class, 'show'])->name('show')->middleware('can:*consultant|*application|*marketing');
    // Route::get('{student}/pdf', [StudentController::class, 'downloadPdf'])->name('pdf')->middleware('can:*consultant|*application|*marketing');
    Route::get('{student}/edit', [StudentController::class, 'edit'])->name('edit')->middleware('can:*consultant|*marketing');
    Route::put('{student}', [StudentController::class, 'update'])->name('update')->middleware('can:*consultant');
    Route::delete('{student}', [StudentController::class, 'destroy'])->name('destroy')->middleware('can:*consultant');
});

// Payment Management
Route::prefix('dashboard/payments')->name('admin.payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/report', [PaymentController::class, 'report'])->name('report')->middleware('can:*accountant');
    Route::get('/create', [PaymentController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [PaymentController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{payment}/edit', [PaymentController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::get('{payment}/download-invoice', [PaymentController::class, 'downloadInvoice'])->name('download-invoice')->middleware('can:*accountant');
    Route::get('/get-application-balance', [PaymentController::class, 'getApplicationBalance'])->name('get-application-balance');
    Route::get('/get-application-invoices', [PaymentController::class, 'getApplicationInvoices'])->name('get-application-invoices');
    Route::put('{payment}', [PaymentController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{payment}', [PaymentController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Commission Management - Accountant Routes
Route::prefix('dashboard/commissions')->name('admin.commissions.')->middleware('can:*accountant')->group(function () {
    Route::get('/', [CommissionController::class, 'index'])->name('index');
    Route::get('/pending', [CommissionController::class, 'pendingClaims'])->name('pending');
    Route::get('/create', [CommissionController::class, 'create'])->name('create');
    Route::get('/{commission}', [CommissionController::class, 'show'])->name('show');
    Route::post('/', [CommissionController::class, 'storeStandalone'])->name('store');
    Route::post('application/{application}', [CommissionController::class, 'store'])->name('store.application');
    Route::get('{commission}/review', [CommissionController::class, 'showForReview'])->name('review');
    Route::post('{commission}/approve', [CommissionController::class, 'approve'])->name('approve');
    Route::post('{commission}/reject', [CommissionController::class, 'reject'])->name('reject');
    Route::post('{commission}/mark-paid', [CommissionController::class, 'markAsPaid'])->name('mark-paid');
    Route::post('bulk-approve', [CommissionController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('{commission}/update-status', [CommissionController::class, 'updateStatus'])->name('update-status');
    Route::delete('{commission}', [CommissionController::class, 'destroy'])->name('destroy');
});

// My Commissions - Employee Routes (All authenticated users can claim)
Route::prefix('dashboard/my-commissions')->name('my-commissions.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\MyCommissionController::class, 'index'])->name('index');
    Route::get('/claimable', [App\Http\Controllers\Admin\MyCommissionController::class, 'claimableApplications'])->name('claimable');
    Route::get('applications/{application}/claim', [App\Http\Controllers\Admin\MyCommissionController::class, 'createClaim'])->name('create-claim');
    Route::post('applications/{application}/claim', [App\Http\Controllers\Admin\MyCommissionController::class, 'storeClaim'])->name('store-claim');
    Route::get('{commission}', [App\Http\Controllers\Admin\MyCommissionController::class, 'show'])->name('show');
    Route::post('{commission}/cancel', [App\Http\Controllers\Admin\MyCommissionController::class, 'cancelClaim'])->name('cancel');
});

// Role Management Overrides
Route::prefix('dashboard/roles')->name('tyro-dashboard.roles.')->group(function () {
    Route::get('/', [LocalRoleController::class, 'index'])->name('index');
    Route::get('/create', [LocalRoleController::class, 'create'])->name('create');
    Route::post('/', [LocalRoleController::class, 'store'])->name('store');
    Route::get('{id}/edit', [LocalRoleController::class, 'edit'])->name('edit');
    Route::put('{id}', [LocalRoleController::class, 'update'])->name('update');
    Route::post('{id}/toggle', [LocalRoleController::class, 'toggleStatus'])->name('toggle');
    Route::delete('{id}', [LocalRoleController::class, 'destroy'])->name('destroy');
});

// Marketing - Lead & Campaign Management
Route::prefix('dashboard/marketing')->name('admin.marketing.')->group(function () {
    Route::get('leads/get-universities', [LeadController::class, 'getUniversities'])->name('leads.get-universities');
    Route::get('leads/get-courses', [LeadController::class, 'getCourses'])->name('leads.get-courses');
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index')->middleware('can:*marketing|*consultant');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create')->middleware('can:*marketing');
    Route::post('leads', [LeadController::class, 'store'])->name('leads.store')->middleware('can:*marketing');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show')->middleware('can:*marketing|*consultant');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit')->middleware('can:*marketing');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update')->middleware('can:*marketing');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy')->middleware('can:*marketing');

    // Campaigns & Assets
    Route::prefix('campaigns')->name('campaigns.')->middleware('can:*digital_marketing')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'store'])->name('store');
        Route::get('{campaign}', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'show'])->name('show');
        Route::get('{campaign}/edit', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'edit'])->name('edit');
        Route::put('{campaign}', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'update'])->name('update');
        Route::post('{campaign}/toggle-boosting', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'toggleBoosting'])->name('toggle-boosting');
        Route::delete('{campaign}', [App\Http\Controllers\Admin\MarketingCampaignController::class, 'destroy'])->name('destroy');

    });

    // Standalone Video Assets
    Route::prefix('videos')->name('videos.')->middleware('can:*digital_marketing')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MarketingVideoController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\MarketingVideoController::class, 'store'])->name('store');
        Route::get('{video}/edit', [App\Http\Controllers\Admin\MarketingVideoController::class, 'edit'])->name('edit');
        Route::put('{video}', [App\Http\Controllers\Admin\MarketingVideoController::class, 'update'])->name('update');
        Route::delete('{video}', [App\Http\Controllers\Admin\MarketingVideoController::class, 'destroy'])->name('destroy');
    });

    // Standalone Poster Assets
    Route::prefix('posters')->name('posters.')->middleware('can:*digital_marketing')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MarketingPosterController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\MarketingPosterController::class, 'store'])->name('store');
        Route::get('{poster}/edit', [App\Http\Controllers\Admin\MarketingPosterController::class, 'edit'])->name('edit');
        Route::put('{poster}', [App\Http\Controllers\Admin\MarketingPosterController::class, 'update'])->name('update');
        Route::delete('{poster}', [App\Http\Controllers\Admin\MarketingPosterController::class, 'destroy'])->name('destroy');
    });

    // Documents (SOP, CV, CL) - Single page management
    Route::prefix('documents')->name('documents.')->middleware('can:*digital_marketing')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MarketingDocumentController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\MarketingDocumentController::class, 'store'])->name('store');
    });
});

// Expense Management
Route::prefix('dashboard/expenses')->name('admin.expenses.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/report', [App\Http\Controllers\Admin\ExpenseController::class, 'report'])->name('report')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\ExpenseController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{expense}/pdf', [App\Http\Controllers\Admin\ExpenseController::class, 'downloadPdf'])->name('download-pdf')->middleware('can:*accountant');
    Route::get('{expense}/edit', [App\Http\Controllers\Admin\ExpenseController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('{expense}', [App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{expense}', [App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
    Route::get('/dashboard/expenses/preview', [App\Http\Controllers\Admin\ExpenseController::class, 'preview'])->name('preview')->middleware('can:*accountant');
    Route::get('/dashboard/expenses/download', [App\Http\Controllers\Admin\ExpenseController::class, 'download'])->name('download')->middleware('can:*accountant');
});

// Office Accounts Management
Route::prefix('dashboard/office-accounts')->name('admin.office-accounts.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\OfficeAccountController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\OfficeAccountController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\OfficeAccountController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{officeAccount}/edit', [App\Http\Controllers\Admin\OfficeAccountController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('{officeAccount}', [App\Http\Controllers\Admin\OfficeAccountController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{officeAccount}', [App\Http\Controllers\Admin\OfficeAccountController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Office Transactions - REMOVED: Migrated to Journal Entries

// Budget Management
Route::prefix('dashboard/budgets')->name('admin.budgets.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BudgetController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\BudgetController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\BudgetController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{budget}/edit', [App\Http\Controllers\Admin\BudgetController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('{budget}', [App\Http\Controllers\Admin\BudgetController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{budget}', [App\Http\Controllers\Admin\BudgetController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Finance Categories - REMOVED: Migrated to Chart of Accounts

// Accounting Periods
Route::prefix('dashboard/accounting-periods')->name('admin.accounting-periods.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::get('/{period}/edit', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::put('{period}', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{period}', [App\Http\Controllers\Admin\AccountingPeriodController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Chart of Accounts
Route::prefix('dashboard/chart-of-accounts')->name('admin.chart-of-accounts.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/{account}/edit', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::post('{account}/status', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'toggleStatus'])->name('status')->middleware('can:*accountant');
    Route::put('{account}', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{account}', [App\Http\Controllers\Admin\ChartOfAccountController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Journal Entries
Route::prefix('dashboard/journal-entries')->name('admin.journal-entries.')->group(function () {
    Route::get('/report', [App\Http\Controllers\Admin\JournalEntryController::class, 'report'])->name('report')->middleware('can:*accountant');
    Route::get('/', [App\Http\Controllers\Admin\JournalEntryController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\JournalEntryController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\JournalEntryController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{journalEntry}', [App\Http\Controllers\Admin\JournalEntryController::class, 'show'])->name('show')->middleware('can:*accountant');
    Route::delete('{journalEntry}', [App\Http\Controllers\Admin\JournalEntryController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Invoices
Route::prefix('dashboard/invoices')->name('admin.invoices.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('index')->middleware('can:*consultant|*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('create')->middleware('can:*consultant|*accountant');
    Route::post('/', [App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('store')->middleware('can:*consultant|*accountant');
    Route::get('{invoice}/edit', [App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::get('{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('show')->middleware('can:*consultant|*accountant');
    Route::delete('{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
    Route::get('/{invoice}/pdf', [App\Http\Controllers\Admin\InvoiceController::class, 'downloadPdf'])->name('download-pdf')->middleware('can:*consultant|*accountant');
});

Route::prefix('dashboard/bank-reconciliations')->name('admin.bank-reconciliations.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BankReconciliationController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\BankReconciliationController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\BankReconciliationController::class, 'store'])->name('store');
    Route::get('/{reconciliation}', [App\Http\Controllers\Admin\BankReconciliationController::class, 'show'])->name('show');
    Route::post('/{reconciliation}/match', [App\Http\Controllers\Admin\BankReconciliationController::class, 'matchItem'])->name('match');
    Route::post('/{reconciliation}/unmatch', [App\Http\Controllers\Admin\BankReconciliationController::class, 'unmatchItem'])->name('unmatch');
    Route::post('/{reconciliation}/close', [App\Http\Controllers\Admin\BankReconciliationController::class, 'close'])->name('close');
    Route::delete('/{reconciliation}', [App\Http\Controllers\Admin\BankReconciliationController::class, 'destroy'])->name('destroy');
});

// VFS Checklist
Route::prefix('dashboard/vfs-checklist')->name('admin.vfs-checklist.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\VfsChecklistController::class, 'index'])->name('index')->middleware('can:*application');
    Route::get('/overview', [App\Http\Controllers\Admin\VfsChecklistController::class, 'overview'])->name('overview')->middleware('can:*application|*consultant');
    Route::get('/overview/{application}', [App\Http\Controllers\Admin\VfsChecklistController::class, 'vfsShow'])->name('vfs-show')->middleware('can:*application');
    Route::get('/overview/{application}/edit', [App\Http\Controllers\Admin\VfsChecklistController::class, 'vfsEdit'])->name('vfs-edit')->middleware('can:*application');
    Route::post('/{application}/vfs-result', [App\Http\Controllers\Admin\VfsChecklistController::class, 'updateVfsResult'])->name('update-vfs-result')->middleware('can:*application');
    Route::get('/templates', [App\Http\Controllers\Admin\VfsChecklistController::class, 'templates'])->name('templates')->middleware('can:*application');
    Route::post('/templates', [App\Http\Controllers\Admin\VfsChecklistController::class, 'storeTemplate'])->name('store-template')->middleware('can:*application');
    Route::put('/templates/{template}', [App\Http\Controllers\Admin\VfsChecklistController::class, 'updateTemplate'])->name('update-template')->middleware('can:*application');
    Route::delete('/templates/{template}', [App\Http\Controllers\Admin\VfsChecklistController::class, 'deleteTemplate'])->name('delete-template')->middleware('can:*application');
    Route::post('/templates/reorder', [App\Http\Controllers\Admin\VfsChecklistController::class, 'reorderTemplates'])->name('reorder-templates')->middleware('can:*application');
    Route::post('/templates/seed', [App\Http\Controllers\Admin\VfsChecklistController::class, 'seedDefaults'])->name('seed-templates')->middleware('can:*application');
    Route::get('/{application}', [App\Http\Controllers\Admin\VfsChecklistController::class, 'show'])->name('show')->middleware('can:*application');
    Route::post('/{application}/items', [App\Http\Controllers\Admin\VfsChecklistController::class, 'storeItem'])->name('store-item')->middleware('can:*application');
    Route::post('/{application}/reset-templates', [App\Http\Controllers\Admin\VfsChecklistController::class, 'resetToTemplates'])->name('reset-templates')->middleware('can:*application');
    Route::post('/{application}/sync-templates', [App\Http\Controllers\Admin\VfsChecklistController::class, 'syncWithTemplates'])->name('sync-templates')->middleware('can:*application');
    Route::post('/items/{item}/toggle', [App\Http\Controllers\Admin\VfsChecklistController::class, 'toggleItem'])->name('toggle-item')->middleware('can:*application');
    Route::put('/items/{item}/notes', [App\Http\Controllers\Admin\VfsChecklistController::class, 'updateNotes'])->name('update-notes')->middleware('can:*application');
    Route::delete('/items/{item}', [App\Http\Controllers\Admin\VfsChecklistController::class, 'deleteItem'])->name('delete-item')->middleware('can:*application');
    Route::post('/{application}/bulk-check', [App\Http\Controllers\Admin\VfsChecklistController::class, 'bulkCheck'])->name('bulk-check')->middleware('can:*application');
    Route::post('/{application}/bulk-uncheck', [App\Http\Controllers\Admin\VfsChecklistController::class, 'bulkUncheck'])->name('bulk-uncheck')->middleware('can:*application');
});

// Financial Reports
Route::prefix('dashboard/reports')->name('admin.reports.')->group(function () {
    Route::get('/summary', [App\Http\Controllers\Admin\ReportController::class, 'summary'])->name('summary')->middleware('can:*accountant');
    Route::get('/balance-sheet', [App\Http\Controllers\Admin\ReportController::class, 'balanceSheet'])->name('balance-sheet')->middleware('can:*accountant');
    Route::get('/balance-sheet/pdf', [App\Http\Controllers\Admin\ReportController::class, 'balanceSheetPdf'])->name('balance-sheet.pdf')->middleware('can:*accountant');
    Route::get('/download-pdf', [App\Http\Controllers\Admin\ReportController::class, 'downloadPdf'])->name('download-pdf')->middleware('can:*accountant');
});

// Currency Management
Route::prefix('dashboard/currencies')->name('admin.currencies.')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('index');
    Route::get('/create', [CurrencyController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [CurrencyController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('{currency}/edit', [CurrencyController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('{currency}', [CurrencyController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('{currency}', [CurrencyController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Salary Management
Route::prefix('dashboard/salaries')->name('admin.salaries.')->group(function () {
    Route::get('/get-employee-details', [App\Http\Controllers\Admin\SalaryController::class, 'getEmployeeDetails'])->name('get-employee-details');
    Route::get('/check-existing', [App\Http\Controllers\Admin\SalaryController::class, 'checkExistingSalary'])->name('check-existing');
    Route::get('/export-excel', [App\Http\Controllers\Admin\SalaryController::class, 'exportExcel'])->name('export-excel')->middleware('can:*accountant');
    Route::get('/export-pdf', [App\Http\Controllers\Admin\SalaryController::class, 'exportPdf'])->name('export-pdf')->middleware('can:*accountant');
    Route::post('/{salary}/mark-paid', [App\Http\Controllers\Admin\SalaryController::class, 'markAsPaid'])->name('mark-paid')->middleware('can:*accountant');
    Route::post('/bulk-store', [App\Http\Controllers\Admin\SalaryController::class, 'bulkStore'])->name('bulk-store')->middleware('can:*accountant');
    Route::post('/bulk-update-basic-salary', [App\Http\Controllers\Admin\SalaryController::class, 'bulkUpdateBasicSalary'])->name('bulk-update-basic-salary')->middleware('can:*accountant');
    Route::post('/bulk-update-account-details', [App\Http\Controllers\Admin\SalaryController::class, 'bulkUpdateAccountDetails'])->name('bulk-update-account-details')->middleware('can:*accountant');
    Route::get('/generate', [App\Http\Controllers\Admin\SalaryController::class, 'generate'])->name('generate')->middleware('can:*accountant');
    Route::get('/bulk-pay-form', [App\Http\Controllers\Admin\SalaryController::class, 'bulkPayForm'])->name('bulk-pay-form')->middleware('can:*accountant');
    Route::post('/bulk-pay', [App\Http\Controllers\Admin\SalaryController::class, 'bulkPay'])->name('bulk-pay')->middleware('can:*accountant');
    Route::get('/', [App\Http\Controllers\Admin\SalaryController::class, 'index'])->name('index')->middleware('can:*accountant');
    Route::get('/create', [App\Http\Controllers\Admin\SalaryController::class, 'create'])->name('create')->middleware('can:*accountant');
    Route::post('/', [App\Http\Controllers\Admin\SalaryController::class, 'store'])->name('store')->middleware('can:*accountant');
    Route::get('/{salary}', [App\Http\Controllers\Admin\SalaryController::class, 'show'])->name('show')->middleware('can:*accountant');
    Route::get('/{salary}/edit', [App\Http\Controllers\Admin\SalaryController::class, 'edit'])->name('edit')->middleware('can:*accountant');
    Route::put('/{salary}', [App\Http\Controllers\Admin\SalaryController::class, 'update'])->name('update')->middleware('can:*accountant');
    Route::delete('/{salary}', [App\Http\Controllers\Admin\SalaryController::class, 'destroy'])->name('destroy')->middleware('can:*accountant');
});

// Application Management
Route::prefix('dashboard/applications')->name('admin.applications.')->group(function () {
    Route::get('/get-student-details', [ApplicationController::class, 'getStudentDetails'])->name('get-student-details');
    Route::get('/get-universities', [ApplicationController::class, 'getUniversities'])->name('get-universities');
    Route::get('/get-courses', [ApplicationController::class, 'getCourses'])->name('get-courses');
    Route::get('/get-intakes', [ApplicationController::class, 'getIntakes'])->name('get-intakes');
    Route::get('/{application}/download-pdf', [ApplicationController::class, 'downloadPdf'])->name('download-pdf')->middleware('can:*consultant|*application');
    Route::get('/{application}/invoice-data', [ApplicationController::class, 'invoiceData'])->name('invoice-data');
    // Route::get('/{application}/invoice', [ApplicationController::class, 'invoice'])->name('invoice')->middleware('can:*consultant|*application');
    Route::get('/country-report', [ApplicationController::class, 'countryReport'])->name('country-report')->middleware('can:*consultant|*application|*accountant');
    Route::get('/', [ApplicationController::class, 'index'])->name('index')->middleware('can:*consultant|*application|*accountant');
    Route::get('/create', [ApplicationController::class, 'create'])->name('create')->middleware('can:*application|*consultant');
    Route::post('/', [ApplicationController::class, 'store'])->name('store')->middleware('can:*application|*consultant');
    Route::get('{application}', [ApplicationController::class, 'show'])->name('show')->middleware('can:*consultant|*application|*accountant');
    Route::get('{application}/edit', [ApplicationController::class, 'edit'])->name('edit')->middleware('can:*application|*accountant');
    Route::put('{application}', [ApplicationController::class, 'update'])->name('update')->middleware('can:*application|*accountant');
    Route::delete('{application}', [ApplicationController::class, 'destroy'])->name('destroy')->middleware('can:*application');
   
});

// Storage file serving route - handled through Laravel route to ensure proper access control
Route::get('/files/preview/{path}', [App\Http\Controllers\FileServingController::class, 'preview'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('preview-file');

Route::get('/files/download/{path}', [App\Http\Controllers\FileServingController::class, 'download'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('download-file');

Route::get('/files/{path}', [App\Http\Controllers\FileServingController::class, 'serveFile'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('serve-file');



