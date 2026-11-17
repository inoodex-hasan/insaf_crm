@extends('admin.layouts.master')

@section('title', 'Partner Commissions')

@section('content')

<div x-data="partnerCommissionIndexHandler()">
    <!-- Page Header -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">Partner Commission</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Institutional University admissions commissions and Partner Agency referral/handover settlements.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.partner-agencies.index') }}" class="btn btn-outline-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Partner Agencies
            </a>
            <a href="{{ route('admin.partner-commissions.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Record Commission
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 flex items-center rounded bg-success/10 p-3 text-success">
            <span class="ltr:mr-2 rtl:ml-2">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-5 rounded bg-danger/10 p-3 text-danger">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
        <!-- University Commission (Receivable / Incoming) -->
        <div class="panel bg-gradient-to-br from-blue-50 to-indigo-50/50 dark:from-blue-900/10 dark:to-indigo-900/10 border-blue-200 dark:border-blue-900/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">University Commission (Receivable)</span>
                <span class="badge bg-blue-600 text-white text-xs">Inflow</span>
            </div>
            <div class="flex items-baseline justify-between mb-2">
                <span class="text-2xl font-bold text-dark dark:text-white-light">৳{{ number_format($totalUniversityReceivable, 2) }}</span>
                <span class="text-xs text-gray-500">Agreed Total</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t border-blue-100 dark:border-blue-900/40">
                <span class="text-success font-semibold">Received: ৳{{ number_format($totalUniversityReceived, 2) }}</span>
                <span class="text-danger font-semibold">Due: ৳{{ number_format(max(0, $totalUniversityReceivable - $totalUniversityReceived), 2) }}</span>
            </div>
        </div>

        <!-- Agency Commission (Payable / Outgoing) -->
        <div class="panel bg-gradient-to-br from-amber-50 to-orange-50/50 dark:from-amber-900/10 dark:to-orange-900/10 border-amber-200 dark:border-amber-900/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Agency Commission (Payable)</span>
                <span class="badge bg-amber-600 text-white text-xs">Outflow</span>
            </div>
            <div class="flex items-baseline justify-between mb-2">
                <span class="text-2xl font-bold text-dark dark:text-white-light">৳{{ number_format($totalAgencyPayable, 2) }}</span>
                <span class="text-xs text-gray-500">Agreed Total</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t border-amber-100 dark:border-amber-900/40">
                <span class="text-success font-semibold">Paid: ৳{{ number_format($totalAgencyPaid, 2) }}</span>
                <span class="text-danger font-semibold">Due: ৳{{ number_format(max(0, $totalAgencyPayable - $totalAgencyPaid), 2) }}</span>
            </div>
        </div>

        <!-- Net Commission Settled -->
        <div class="panel bg-gradient-to-br from-emerald-50 to-teal-50/50 dark:from-emerald-900/10 dark:to-teal-900/10 border-emerald-200 dark:border-emerald-900/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Net Commission Settled</span>
                <span class="badge bg-emerald-600 text-white text-xs">Realized Profit</span>
            </div>
            <div class="flex items-baseline justify-between mb-2">
                <span class="text-2xl font-bold {{ ($totalUniversityReceived - $totalAgencyPaid) >= 0 ? 'text-success' : 'text-danger' }}">
                    ৳{{ number_format($totalUniversityReceived - $totalAgencyPaid, 2) }}
                </span>
                <span class="text-xs text-gray-500">Inflow - Outflow</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t border-emerald-100 dark:border-emerald-900/40">
                <span class="text-gray-600 dark:text-gray-400">Receivable Pending: ৳{{ number_format($totalReceivableDue, 2) }}</span>
                <span class="text-gray-600 dark:text-gray-400">Payable Pending: ৳{{ number_format($totalPayableDue, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Commissions Table & Compact 1-Line Filter Bar -->
    <div class="panel">
        <div class="mb-5">
            <form action="{{ route('admin.partner-commissions.index') }}" method="GET"
                  style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; width: 100%;">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif

                <!-- Search bar with inline submit icon -->
                <div class="relative" style="flex: 1; min-width: 180px;">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-input text-xs ltr:pr-10 rtl:pl-10" />
                    <button type="submit" class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-3 rtl:left-3 text-gray-400">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </button>
                </div>

                @if(!request('type'))
                <!-- Partner Type (only when on All view) -->
                <select name="type" class="form-select text-xs" style="width: 130px; flex-shrink: 0;">
                    <option value="">All Types</option>
                    <option value="university" {{ request('type') === 'university' ? 'selected' : '' }}>University</option>
                    <option value="agency" {{ request('type') === 'agency' ? 'selected' : '' }}>Partner Agency</option>
                </select>
                @endif

                <!-- Workflow Stage -->
                <select name="workflow_status" class="form-select text-xs" style="width: 130px; flex-shrink: 0;">
                    <option value="">All Stages</option>
                    <option value="pending" {{ request('workflow_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('workflow_status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="visa_approved" {{ request('workflow_status') === 'visa_approved' ? 'selected' : '' }}>Visa Approved</option>
                    <option value="settled" {{ request('workflow_status') === 'settled' ? 'selected' : '' }}>Settled</option>
                    <option value="cancelled" {{ request('workflow_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <!-- Payment Status -->
                <select name="payment_status" class="form-select text-xs" style="width: 130px; flex-shrink: 0;">
                    <option value="">All Payments</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Fully Paid</option>
                </select>

                <div class="flex items-center" style="gap: 4px; flex-shrink: 0;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.partner-commissions.index', request('type') ? ['type' => request('type')] : []) }}" class="btn btn-outline-danger btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Details</th>
                        <th>Partner & Channel</th>
                        <th>Agreed Rate</th>
                        <th>Total Amount</th>
                        <th>Paid / Due (BDT)</th>
                        <th>Processing Stage</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commissions as $comm)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($comm->student)
                                    <div class="font-bold text-dark dark:text-white-light">
                                        <a href="{{ route('admin.partner-commissions.show', $comm->id) }}" class="hover:text-primary hover:underline">
                                            {{ $comm->student->first_name }} {{ $comm->student->last_name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Passport: <span class="font-medium text-dark dark:text-gray-300">{{ $comm->student->passport_number ?: '—' }}</span>
                                    </div>
                                    @if($comm->student->phone)
                                        <div class="text-xs text-gray-400">{{ $comm->student->phone }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Student #{{ $comm->student_id }}</span>
                                @endif
                            </td>
                            <td>
                                @if($comm->commission_type === 'university')
                                    <div class="font-semibold text-primary">
                                        {{ $comm->university->name ?? 'University' }}
                                    </div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="badge bg-primary/10 text-primary border border-primary/20 text-[10px]">
                                            University Direct
                                        </span>
                                        @if($comm->country)
                                            <span class="text-xs text-gray-500">({{ $comm->country->name }})</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="font-semibold text-dark dark:text-white-light">
                                        {{ $comm->partnerAgency->name ?? 'Partner Agency' }}
                                    </div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        @if($comm->agency_referral_type === 'outbound_handover')
                                            <span class="badge bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-300 text-[10px]">
                                                Outbound Handover
                                            </span>
                                        @else
                                            <span class="badge bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-300 text-[10px]">
                                                Inbound Sub-agent
                                            </span>
                                        @endif
                                        @if($comm->country)
                                            <span class="text-xs text-gray-500">({{ $comm->country->name }})</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($comm->rate_type === 'percentage')
                                    <div class="font-semibold text-dark dark:text-white-light">
                                        {{ $comm->rate_value }}%
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Base: {{ $comm->currency }} {{ number_format($comm->base_amount, 2) }}
                                    </div>
                                @else
                                    <div class="font-semibold text-dark dark:text-white-light">
                                        Fixed {{ $comm->currency }} {{ number_format($comm->rate_value, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-dark dark:text-white-light">
                                    ৳{{ number_format($comm->total_amount_bdt, 2) }}
                                </div>
                                @if($comm->currency !== 'BDT')
                                    <div class="text-xs text-gray-400">
                                        {{ $comm->currency }} {{ number_format($comm->total_commission, 2) }} @ {{ $comm->exchange_rate }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="text-xs text-success font-semibold">Paid: ৳{{ number_format($comm->paid_amount_bdt, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-xs {{ $comm->due_amount_bdt > 0 ? 'text-danger font-semibold' : 'text-gray-400' }}">
                                        Due: ৳{{ number_format($comm->due_amount_bdt, 2) }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    @if($comm->payment_status === 'paid')
                                        <span class="badge bg-success text-[10px]">Paid</span>
                                    @elseif($comm->payment_status === 'partial')
                                        <span class="badge bg-warning text-[10px]">Partial</span>
                                    @else
                                        <span class="badge bg-danger text-[10px]">Unpaid</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    @php
                                        $wfClasses = [
                                            'pending' => 'bg-secondary',
                                            'processing' => 'bg-info',
                                            'visa_approved' => 'bg-primary',
                                            'settled' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                        ];
                                    @endphp
                                    <span class="badge {{ $wfClasses[$comm->workflow_status] ?? 'bg-secondary' }} text-[10px] capitalize">
                                        {{ str_replace('_', ' ', $comm->workflow_status) }}
                                    </span>
                                </div>
                                @if($comm->processing_stage)
                                    <div class="text-xs text-gray-500 mt-1 max-w-[150px] truncate" title="{{ $comm->processing_stage }}">
                                        {{ $comm->processing_stage }}
                                    </div>
                                @endif
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
                                            <a href="{{ route('admin.partner-commissions.show', $comm->id) }}" class="flex w-full items-center px-4 py-2 text-left text-dark hover:bg-gray-100 dark:text-white-light dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View Processing
                                            </a>
                                        </li>
                                        @if($comm->due_amount_bdt > 0)
                                        <li>
                                            <button type="button" 
                                                    class="flex w-full items-center px-4 py-2 text-left text-success hover:bg-gray-100 dark:hover:bg-dark/40"
                                                    @click="toggle(); openSettleModal(@js($comm))">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                                </svg>
                                                Settle Payment
                                            </button>
                                        </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('admin.partner-commissions.edit', $comm->id) }}" class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:text-white-light dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit Record
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.partner-commissions.destroy', $comm->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this commission record?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
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
                            <td colspan="8" class="text-center py-10 text-gray-500">
                                No partner commissions found matching the criteria. Click "Record Commission" to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $commissions->links() }}
        </div>
    </div>

    <!-- Quick Settle Modal (Compliant with AGENTS.md Rule 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="settleModalOpen" class="modal-overlay-full" style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="settleModalOpen = false"
                     x-show="settleModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="currentComm?.direction === 'receivable' ? 'Receive Commission Payment' : 'Pay Agency Commission'"></h5>
                            <p class="text-xs text-gray-500" x-text="'Student: ' + (currentComm?.student?.first_name || '') + ' ' + (currentComm?.student?.last_name || '')"></p>
                        </div>
                        <button type="button" @click="settleModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form :action="settleAction" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-[#0e1726] text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Agreed Total:</span>
                                <span class="font-bold text-dark dark:text-white-light" x-text="'৳' + Number(currentComm?.total_amount_bdt || 0).toLocaleString()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Current Due:</span>
                                <span class="font-bold text-danger" x-text="'৳' + Number(currentComm?.due_amount_bdt || 0).toLocaleString()"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                                Office Account (Bank / Cash) <span class="text-danger">*</span>
                            </label>
                            <select name="office_account_id" required class="form-select">
                                <option value="">Select Office Account</option>
                                @foreach($officeAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }} ({{ ucfirst($account->account_type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                                    Settlement Amount (BDT) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="1" :max="currentComm?.due_amount_bdt || 1" name="amount_bdt" x-model="settleAmount" required class="form-input font-bold" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">
                                    Settlement Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="settlement_date" value="{{ date('Y-m-d') }}" required class="form-input" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online / Wire</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Reference / Voucher No</label>
                                <input type="text" name="reference_no" class="form-input" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Settlement Notes</label>
                            <textarea name="notes" rows="2" class="form-textarea"></textarea>
                        </div>

                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                            <button type="button" @click="settleModalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-success" x-text="currentComm?.direction === 'receivable' ? 'Record Inflow' : 'Record Outflow'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function partnerCommissionIndexHandler() {
    return {
        settleModalOpen: false,
        currentComm: null,
        settleAmount: 0,
        settleAction: '',
        openSettleModal(comm) {
            this.currentComm = comm;
            this.settleAmount = comm.due_amount_bdt;
            this.settleAction = '{{ url('dashboard/partner-commissions') }}/' + comm.id + '/settle';
            this.settleModalOpen = true;
        }
    };
}
</script>
@endpush

@endsection
