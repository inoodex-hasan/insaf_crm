@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Staff Attendance Tracking Widget (Visible for regular staff, hidden for Super Admin) -->
    @include('admin.attendance.partials.dashboard-widget')

    {{-- Individual User Daily Operations Dashboard (Accessible for application, consultant, marketing, admin, super-admin) --}}
    @php
        $authUser = auth()->user();
        $isOperationalStaff = $authUser && (
            $authUser->hasRole('application') ||
            $authUser->hasRole('consultant') ||
            $authUser->hasRole('marketing')
        );
        $isAdminUser = $authUser && ($authUser->isAdminOrSuperAdmin() || $authUser->hasRole('admin') || $authUser->hasRole('super-admin') || $authUser->hasRole('superadmin'));
    @endphp

    {{-- For application, consultant, and marketing roles (operational staff), show at the top --}}
    @if(!empty($userOperations) && $isOperationalStaff && !$isAdminUser)
        @include('admin.dashboard.partials.user-operations-widget')
    @endif

    {{-- For Admin / Super Admin: If inspecting a staff member, show their Operations Dashboard right at the top --}}
    @if(!empty($userOperations) && $isAdminUser && !empty($isInspectingOther))
        @include('admin.dashboard.partials.user-operations-widget')
    @endif

    {{-- For Admin / Super Admin: If viewing own overview, show Quick Staff Operations Switcher Bar at the top --}}
    @if($isAdminUser && empty($isInspectingOther))
        @include('admin.dashboard.partials.admin-staff-quick-bar')
    @endif

    <!-- Application & Admission Pipeline Intelligence Filter Widget -->
    @include('admin.dashboard.partials.pipeline-filter-widget')

    <!-- Breadcrumb -->
    @if (auth()->check() && auth()->user()->hasRole('admin'))
        <h5 class="text-xs font-semibold text-[#506690]">Administration</h5>
        <div class="pt-5">
            <!-- Stats Grid -->
            <div class="mb-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('tyro-dashboard.roles.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-success">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_roles'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Roles</h5>
                        </div>
                    </div>
                </a>

                <a href="{{ route('tyro-dashboard.privileges.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-info">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_privileges'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Privileges</h5>
                        </div>
                    </div>
                </a>

                <a href="{{ route('tyro-dashboard.users.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-warning">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                    <ellipse opacity="0.5" cx="12" cy="17" rx="7" ry="4"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_users'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Users</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    @if (
        (auth()->check() && auth()->user()->hasRole('editor')) ||
            auth()->user()->hasRole('admin') ||
            auth()->user()->hasRole('super-admin'))
        <h5 class="mt-6 text-xs font-semibold text-[#506690]">Education Data</h5>
        <div class="pb-6 pt-5">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4 dashboard-grid-4">
                <!-- Country -->
                <a href="{{ route('admin.countries.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-info">
                                <svg width="60" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 3C7.03 3 3 7.03 3 12C3 16.97 7.03 21 12 21C16.97 21 21 16.97 21 12C21 7.03 16.97 3 12 3Z"
                                        stroke="currentColor" stroke-width="1.5" />
                                    <path d="M3 12H21" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M12 3C14.5 6 14.5 18 12 21C9.5 18 9.5 6 12 3Z" stroke="currentColor"
                                        stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_countries'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Countries</h5>
                        </div>
                    </div>
                </a>

                <!-- University -->
                <a href="{{ route('admin.universities.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-info">
                                <svg width="60" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">

                                    <!-- University -->
                                    <path d="M3 10L12 4L21 10" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />

                                    <path d="M6 10V18M10 10V18M14 10V18M18 10V18" stroke="#16A34A" stroke-width="1.8"
                                        stroke-linecap="round" />

                                    <path d="M4 18H20" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" />
                                </svg>

                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_universities'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Universities</h5>
                        </div>
                    </div>
                </a>

                <!-- Course -->
                <a href="{{ route('admin.courses.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-info">
                                <svg width="60" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">

                                    <!-- Book -->
                                    <path
                                        d="M5 6C5 5.45 5.45 5 6 5H11C12.1 5 13 5.9 13 7V18
                                                                                                                                                                                                                                                                                                                                         C13 16.9 12.1 16 11 16H6C5.45 16 5 16.45 5 17V6Z"
                                        stroke="#9cd21fff" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />

                                    <path
                                        d="M19 6C19 5.45 18.55 5 18 5H13C11.9 5 11 5.9 11 7V18
                                                                                                                                                                                                                                                                                                                                         C11 16.9 11.9 16 13 16H18C18.55 16 19 16.45 19 17V6Z"
                                        stroke="#9cd21fff" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>


                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_courses'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Courses</h5>
                        </div>
                    </div>
                </a>

                <!-- Course Intakes -->
                <a href="{{ route('admin.course-intakes.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-warning">
                                <svg width="60" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M8 14H8.01M12 14H12.01M16 14H16.01M8 18H8.01M12 18H12.01M16 18H16.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_course_intakes'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Course Intakes</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    @if ((auth()->check() && auth()->user()->hasRole('admin')) || auth()->user()->hasRole('super-admin'))
        <h5 class="mt-6 text-xs font-semibold text-[#506690]">Overall Overview</h5>
        <div class="pb-6 pt-5">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4 dashboard-grid-4">
                <!-- Total Leads -->
                <a href="{{ route('admin.marketing.leads.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-warning">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" x="2" y="5" width="20" height="14"
                                        rx="2" />
                                    <circle stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" cx="8" cy="12" r="2.5" />
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M14 10h4M14 14h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_leads'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Leads</h5>
                        </div>
                    </div>
                </a>

                <!-- Active Students -->
                <a href="{{ route('admin.students.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-info">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M22 10l-10-5-10 5 10 5 10-5z" />
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M6 12v5c3 3 9 3 12 0v-5" />
                                    <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="22"
                                        y1="10" x2="22" y2="16" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_students'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Active Students</h5>
                        </div>
                    </div>
                </a>

                <!-- Total Applications -->
                <a href="{{ route('admin.applications.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-primary">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" points="14 2 14 8 20 8" />
                                    <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="16"
                                        y1="13" x2="8" y2="13" />
                                    <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="16"
                                        y1="17" x2="8" y2="17" />
                                    <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="10"
                                        y1="9" x2="8" y2="9" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_applications'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Applications</h5>
                        </div>
                    </div>
                </a>

                <!-- Pending Applications -->
                <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-warning">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M12 8v4l3 3" />
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">
                                {{ number_format($stats['pending_applications'] ?? 0) }}</p>
                            <h5 class="text-xs font-semibold text-[#506690]">Pending Applications</h5>
                        </div>
                    </div>
                </a>

                <!-- Total Invoices -->
                <a href="{{ route('admin.invoices.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-primary">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M7 8h10M7 12h10M7 16h6" />
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9l-6-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_invoices'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Invoices</h5>
                        </div>
                    </div>
                </a>

                <!-- Total Payments -->
                <a href="{{ route('admin.payments.index') }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-success">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M3 7h18M3 11h18" />
                                    <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor"
                                        stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_payments'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Payments</h5>
                        </div>
                    </div>
                </a>

                <!-- Pending Payments -->
                <a href="{{ route('admin.payments.index', ['payment_status' => 'pending']) }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-success">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M12 8v4l3 3" />
                                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" d="M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['pending_payments'] ?? 0) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Pending Payments</h5>
                        </div>
                    </div>
                </a>

                <!-- Total Revenue -->
                <a href="{{ route('admin.payments.index', ['payment_status' => 'completed']) }}" class="panel h-full sm:col-span-2 xl:col-span-1 block transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer text-inherit">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <div class="text-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="w-full ltr:ml-3 rtl:mr-3">
                            <p class="dark:text-white-light text-xl">{{ number_format($stats['total_revenue'] ?? 0, 2) }}
                            </p>
                            <h5 class="text-xs font-semibold text-[#506690]">Total Revenue</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    {{-- For admin and super admin, show User Operations Dashboard at the bottom when not in inspection mode --}}
    @if(!empty($userOperations) && $isAdminUser && empty($isInspectingOther))
        <div class="mt-8">
            @include('admin.dashboard.partials.user-operations-widget')
        </div>
    @endif
    </div>
@endsection

@push('styles')
<style>
@media (min-width: 1024px) {
    .dashboard-grid-4 {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    }
}
</style>
@endpush
