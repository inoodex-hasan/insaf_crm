@extends('admin.layouts.master')

@section('title', 'Expenses')

@section('content')

<div>
    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wider text-dark dark:text-white-light">Expense & Investment Management</h2>
            <p class="text-xs text-white-dark mt-1">Track operational office expenses alongside foreign university partnership tours and staff investments.</p>
        </div>
        <div class="flex w-full flex-wrap items-center justify-end gap-3 sm:w-auto">
            <a href="{{ route('admin.expenses.preview', request()->query()) }}" class="btn btn-outline-primary gap-2" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Preview
            </a>
            <a href="{{ route('admin.expenses.download', request()->query()) }}" class="btn btn-outline-success gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Record Expense
            </a>
        </div>
    </div>

    {{-- Summary Metric Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-5">
        {{-- Card 1: Total Expenses --}}
        <div class="panel p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold text-white-dark tracking-wider">Total Expenses (All)</span>
                    <div class="text-2xl font-black text-danger mt-1">BDT {{ number_format($totalExpenses, 2) }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-danger/10 text-danger">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
            <div class="text-[11px] text-white-dark mt-2">Combined operational outlays & foreign tour investments</div>
        </div>

        {{-- Card 2: General / Operational Expenses --}}
        <div class="panel p-4 rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#1b2e4b]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold text-white-dark tracking-wider">General Operations</span>
                    <div class="text-2xl font-black text-primary mt-1">BDT {{ number_format($totalGeneral, 2) }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
            </div>
            <div class="text-[11px] text-white-dark mt-2">Office rent, utilities, salaries, stationery, marketing</div>
        </div>

        {{-- Card 3: Tour & Partnership Investments --}}
        <div class="panel p-4 rounded-xl border border-purple-200 dark:border-purple-900/40 bg-purple-50/40 dark:bg-purple-950/20">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase font-bold text-purple-700 dark:text-purple-300 tracking-wider">Tour & Staff Investments</span>
                    <div class="text-2xl font-black text-purple-800 dark:text-purple-200 mt-1">BDT {{ number_format($totalInvestments, 2) }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-300">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13"></path>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </div>
            </div>
            <div class="text-[11px] text-purple-600/80 dark:text-purple-400 mt-2">Foreign university delegations, spot admissions & negotiations</div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel mt-6">
        <div class="mb-5">
            <form action="{{ route('admin.expenses.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    {{-- Search --}}
                    <div class="relative sm:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search description, staff, purpose, method..." class="form-input ltr:pr-11 rtl:pl-11" />
                        <button type="submit"
                            class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11.5" cy="11.5" r="9.5" opacity="0.5" />
                                <path d="M18.5 18.5L22 22" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    {{-- Type Filter --}}
                    <div>
                        <select name="investment_filter" class="form-select">
                            <option value="">All Expense Types</option>
                            <option value="general" {{ request('investment_filter') === 'general' ? 'selected' : '' }}>General Expenses Only</option>
                            <option value="investment" {{ request('investment_filter') === 'investment' ? 'selected' : '' }}>Tour Investments Only</option>
                        </select>
                    </div>

                    {{-- Staff Filter --}}
                    <div>
                        <select name="staff_id" class="form-select">
                            <option value="">All Staff / Delegates</option>
                            @foreach ($staffList as $staff)
                                <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Country Filter --}}
                    <div>
                        <select name="country_id" class="form-select">
                            <option value="">All Countries</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-white-dark font-medium">Date Range:</span>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input text-xs w-36 py-1.5" placeholder="Start Date" />
                        <span class="text-xs text-white-dark">to</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input text-xs w-36 py-1.5" placeholder="End Date" />
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary py-1.5 px-6 text-xs">Apply Filters</button>
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-danger py-1.5 px-4 text-xs">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Datatable --}}
        <div class="datatable">
            <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description & Delegation</th>
                            <th>Type & Category</th>
                            <th class="text-right">Amount</th>
                            <th>Payment Method</th>
                            <th>Recorded By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="whitespace-nowrap font-medium text-xs">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td>
                                    @if($expense->is_investment)
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 font-bold text-[10px] uppercase tracking-wider py-0.5 px-2">
                                                Tour Investment
                                            </span>
                                            @if($expense->investment_purpose)
                                                <span class="text-xs font-semibold text-purple-900 dark:text-purple-200">
                                                    {{ $expense->investment_purpose }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="font-bold text-sm text-dark dark:text-white-light">
                                        {{ $expense->description }}
                                    </div>
                                    @if($expense->is_investment)
                                        <div class="text-[11px] text-white-dark mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                            <span>Staff: <strong class="text-slate-700 dark:text-slate-300">{{ $expense->staff->name ?? 'Unassigned' }}</strong></span>
                                            @if($expense->country)
                                                <span>&bull;</span>
                                                <span class="text-slate-700 dark:text-slate-300">
                                                    {{ $expense->country->name }}
                                                </span>
                                            @endif
                                            @if($expense->university)
                                                <span class="text-primary font-medium">({{ $expense->university->name }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline-info text-xs">
                                        {{ $expense->chartOfAccount->name ?? 'General' }}
                                    </span>
                                </td>
                                <td class="font-bold text-danger text-right whitespace-nowrap">
                                    BDT {{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="capitalize text-xs whitespace-nowrap">
                                    {{ str_replace('_', ' ', $expense->payment_method) ?: '-' }}
                                    @if($expense->account)
                                        <div class="text-[10px] text-white-dark">{{ $expense->account->account_name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-xs text-white-dark">
                                        {{ $expense->creator->name ?? 'System' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                            <li>
                                                <a href="{{ route('admin.expenses.download-pdf', $expense->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                    </svg>
                                                    Download Voucher
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.expenses.edit', $expense->id) }}"
                                                   class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this expense record?');" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-white-dark">
                                    No expenses or staff tour investments found matching the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
