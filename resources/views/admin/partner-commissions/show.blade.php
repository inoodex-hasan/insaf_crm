@extends('admin.layouts.master')

@section('title', 'Partner Commission - File Processing Details')

@section('content')

<div x-data="partnerCommissionShowHandler()" class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.partner-commissions.index') }}" class="text-gray-500 hover:text-primary transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">
                    {{ $partnerCommission->student ? $partnerCommission->student->first_name . ' ' . $partnerCommission->student->last_name : 'Partner Deal #' . $partnerCommission->id }}
                </h2>
                <!-- Badges -->
                @if($partnerCommission->direction === 'receivable')
                    <span class="badge bg-success/10 text-success border border-success/30 font-bold">Receivable (Inflow)</span>
                @else
                    <span class="badge bg-danger/10 text-danger border border-danger/30 font-bold">Payable (Outflow)</span>
                @endif

                @if($partnerCommission->commission_type === 'university')
                    <span class="badge bg-primary/10 text-primary border border-primary/30">University Direct</span>
                @else
                    <span class="badge bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-300">
                        {{ $partnerCommission->agency_referral_type === 'outbound_handover' ? 'Outbound Handover' : 'Inbound Sub-Agent' }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Agreement Ref: <strong class="text-dark dark:text-gray-300">{{ $partnerCommission->agreement_reference ?: 'N/A' }}</strong> |
                Created: {{ $partnerCommission->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($partnerCommission->due_amount_bdt > 0)
                <button type="button" @click="settleModalOpen = true" class="btn btn-success">
                    <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    {{ $partnerCommission->direction === 'receivable' ? 'Receive Payment' : 'Pay Commission' }}
                </button>
            @endif

            <a href="{{ route('admin.partner-commissions.edit', $partnerCommission->id) }}" class="btn btn-outline-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Deal
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center rounded bg-success/10 p-3 text-success">
            <span class="ltr:mr-2 rtl:ml-2">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="rounded bg-danger/10 p-3 text-danger">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Financial Metric Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="panel p-4">
            <div class="text-xs text-gray-500 font-semibold uppercase">Total Deal Value (BDT)</div>
            <div class="text-2xl font-extrabold text-dark dark:text-white-light mt-1">
                ৳{{ number_format($partnerCommission->total_amount_bdt, 2) }}
            </div>
            @if($partnerCommission->currency !== 'BDT')
                <div class="text-xs text-gray-400 mt-0.5">
                    {{ $partnerCommission->currency }} {{ number_format($partnerCommission->total_commission, 2) }} (Rate: {{ $partnerCommission->exchange_rate }})
                </div>
            @endif
        </div>

        <div class="panel p-4">
            <div class="text-xs text-gray-500 font-semibold uppercase">Total Paid / Settled</div>
            <div class="text-2xl font-extrabold text-success mt-1">
                ৳{{ number_format($partnerCommission->paid_amount_bdt, 2) }}
            </div>
            <div class="text-xs text-gray-400 mt-0.5">
                {{ $partnerCommission->settlements->count() }} transaction(s)
            </div>
        </div>

        <div class="panel p-4">
            <div class="text-xs text-gray-500 font-semibold uppercase">Remaining Due Balance</div>
            <div class="text-2xl font-extrabold {{ $partnerCommission->due_amount_bdt > 0 ? 'text-danger' : 'text-gray-400' }} mt-1">
                ৳{{ number_format($partnerCommission->due_amount_bdt, 2) }}
            </div>
            <div class="text-xs text-gray-400 mt-0.5">
                Status: <span class="uppercase font-semibold">{{ $partnerCommission->payment_status }}</span>
            </div>
        </div>

        <div class="panel p-4">
            <div class="text-xs text-gray-500 font-semibold uppercase">File Processing Stage</div>
            <div class="text-lg font-bold text-primary mt-1 truncate" title="{{ $partnerCommission->processing_stage }}">
                {{ $partnerCommission->processing_stage ?: 'In Progress' }}
            </div>
            <div class="text-xs text-gray-400 mt-0.5">
                Workflow: <span class="font-semibold capitalize">{{ str_replace('_', ' ', $partnerCommission->workflow_status) }}</span>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Information Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column 1: Student Case & Processing History -->
        <div class="space-y-6">
            <!-- Student Case Profile Card -->
            <div class="panel">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Student File & Case Profile
                    </h5>
                    @if($partnerCommission->student)
                        <a href="{{ route('admin.students.show', $partnerCommission->student->id) }}" target="_blank" class="text-xs text-primary hover:underline">
                            View Full Student Profile →
                        </a>
                    @endif
                </div>

                @if($partnerCommission->student)
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm">
                        <div>
                            <span class="text-xs text-gray-500 block">Full Name</span>
                            <span class="font-bold text-dark dark:text-white-light">
                                {{ $partnerCommission->student->first_name }} {{ $partnerCommission->student->last_name }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Passport Number</span>
                            <span class="font-semibold text-dark dark:text-gray-300">
                                {{ $partnerCommission->student->passport_number ?: 'Not provided' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Phone / WhatsApp</span>
                            <span class="font-semibold text-dark dark:text-gray-300">
                                {{ $partnerCommission->student->phone ?: '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Destination Country</span>
                            <span class="font-semibold text-primary">
                                {{ $partnerCommission->country->name ?? $partnerCommission->student->country->name ?? '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Assigned Counselor</span>
                            <span class="font-medium text-dark dark:text-gray-300">
                                {{ $partnerCommission->student->consultantAssignee->name ?? 'Unassigned' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Application Officer</span>
                            <span class="font-medium text-dark dark:text-gray-300">
                                {{ $partnerCommission->student->applicationAssignee->name ?? 'Unassigned' }}
                            </span>
                        </div>
                    </div>

                    <!-- Linked University Application Details -->
                    @if($partnerCommission->student->applications && $partnerCommission->student->applications->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h6 class="text-xs font-bold uppercase text-gray-500 mb-2">CRM Application Record</h6>
                            @foreach($partnerCommission->student->applications as $app)
                                <div class="rounded-lg bg-gray-50 p-3 dark:bg-[#0e1726] text-xs space-y-1 mb-2">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-dark dark:text-white-light">
                                            {{ $app->university->name ?? 'University' }}
                                        </span>
                                        <span class="badge bg-primary/10 text-primary text-[10px]">{{ $app->visa_status ?: 'In Progress' }}</span>
                                    </div>
                                    <div class="text-gray-500">
                                        Tuition Fee: <strong>৳{{ number_format($app->tuition_fee, 2) }}</strong> |
                                        Offer Letter: {{ $app->offer_letter_received ? 'Received (' . ($app->offer_letter_received_date ?? 'Yes') . ')' : 'Pending' }}
                                    </div>
                                    @if($app->visa_approval_date)
                                        <div class="text-success font-semibold">
                                            Visa Approved On: {{ $app->visa_approval_date }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-gray-500 text-sm">No linked student found.</div>
                @endif
            </div>

            <!-- Partner Identity & Handover Details -->
            <div class="panel">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <polyline points="17 11 19 13 23 9"/>
                        </svg>
                        Partner Identity & Handover Agreement
                    </h5>
                </div>

                @if($partnerCommission->commission_type === 'university')
                    <!-- University Profile -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">University Name:</span>
                            <span class="font-bold text-primary">{{ $partnerCommission->university->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Destination Country:</span>
                            <span class="font-semibold text-dark dark:text-gray-300">{{ $partnerCommission->country->name ?? $partnerCommission->university->country->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Channel Type:</span>
                            <span class="badge bg-primary/10 text-primary">Direct Institutional Commission</span>
                        </div>
                    </div>
                @else
                    <!-- Partner Agency Profile -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Agency Name:</span>
                            <span class="font-bold text-dark dark:text-white-light">{{ $partnerCommission->partnerAgency->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Contact Person:</span>
                            <span class="font-semibold text-dark dark:text-gray-300">{{ $partnerCommission->partnerAgency->contact_person ?: '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Phone / WhatsApp:</span>
                            <span class="font-semibold text-dark dark:text-gray-300">{{ $partnerCommission->partnerAgency->phone ?: '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Collaboration Type:</span>
                            <span class="badge {{ $partnerCommission->agency_referral_type === 'outbound_handover' ? 'bg-amber-500/10 text-amber-700 border border-amber-300' : 'bg-cyan-500/10 text-cyan-700 border border-cyan-300' }}">
                                {{ $partnerCommission->agency_referral_type === 'outbound_handover' ? 'Outbound Handover to Agency' : 'Inbound Referral from Sub-Agent' }}
                            </span>
                        </div>
                        @if($partnerCommission->partnerAgency && $partnerCommission->partnerAgency->specialized_countries)
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Specialized Countries:</span>
                                <span class="text-xs font-semibold text-primary">{{ $partnerCommission->partnerAgency->specialized_countries }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Handover Date:</span>
                        <span class="font-semibold text-dark dark:text-gray-300">
                            {{ $partnerCommission->handover_date ? $partnerCommission->handover_date->format('d M Y') : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Handover Officer:</span>
                        <span class="font-semibold text-dark dark:text-gray-300">
                            {{ $partnerCommission->handoverOfficer->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Agreement Reference:</span>
                        <span class="font-mono text-xs font-bold text-dark dark:text-gray-300">
                            {{ $partnerCommission->agreement_reference ?: '—' }}
                        </span>
                    </div>
                </div>

                @if($partnerCommission->processing_notes)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-xs text-gray-500 block mb-1 font-semibold uppercase">Processing Notes & Handover Terms</span>
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-[#0e1726] text-xs text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                            {{ $partnerCommission->processing_notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Column 2: Financial Terms & Settlement History -->
        <div class="space-y-6">
            <!-- Commission Terms & Economics Card -->
            <div class="panel">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        Financial Terms & Calculation
                    </h5>
                    <span class="badge {{ $partnerCommission->direction === 'receivable' ? 'bg-success' : 'bg-danger' }} text-xs capitalize">
                        {{ $partnerCommission->direction }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Agreed Rate Structure:</span>
                        <span class="font-bold text-dark dark:text-white-light">
                            @if($partnerCommission->rate_type === 'percentage')
                                {{ $partnerCommission->rate_value }}% of {{ $partnerCommission->currency }} {{ number_format($partnerCommission->base_amount, 2) }}
                            @else
                                Fixed {{ $partnerCommission->currency }} {{ number_format($partnerCommission->rate_value, 2) }}
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Commission in Foreign Currency:</span>
                        <span class="font-semibold text-dark dark:text-gray-300">
                            {{ $partnerCommission->currency }} {{ number_format($partnerCommission->total_commission, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Exchange Rate:</span>
                        <span class="font-semibold text-dark dark:text-gray-300">
                            1 {{ $partnerCommission->currency }} = {{ $partnerCommission->exchange_rate }} BDT
                        </span>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-sm font-bold text-dark dark:text-white-light">Total Converted Value (BDT):</span>
                        <span class="text-lg font-extrabold text-primary">৳{{ number_format($partnerCommission->total_amount_bdt, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Settled (BDT):</span>
                        <span class="font-bold text-success">৳{{ number_format($partnerCommission->paid_amount_bdt, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Outstanding Due (BDT):</span>
                        <span class="font-bold text-danger">৳{{ number_format($partnerCommission->due_amount_bdt, 2) }}</span>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $pct = $partnerCommission->total_amount_bdt > 0 ? min(100, round(($partnerCommission->paid_amount_bdt / $partnerCommission->total_amount_bdt) * 100)) : 0;
                    @endphp
                    <div class="pt-2">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Settlement Progress</span>
                            <span>{{ $pct }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full bg-success transition-all duration-300" style="width: {{ $pct }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Account Settlements History Card -->
            <div class="panel">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                        Office Account Settlements & Ledger
                    </h5>
                    @if($partnerCommission->due_amount_bdt > 0)
                        <button type="button" @click="settleModalOpen = true" class="btn btn-sm btn-outline-success">
                            + Add Settlement
                        </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table-auto w-full text-xs">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Office Account</th>
                                <th>Method / Ref</th>
                                <th class="text-right">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partnerCommission->settlements as $settlement)
                                <tr>
                                    <td>{{ $settlement->settlement_date ? $settlement->settlement_date->format('d M Y') : '—' }}</td>
                                    <td>
                                        <span class="badge {{ $settlement->settlement_type === 'received' ? 'bg-success' : 'bg-danger' }} text-[10px] capitalize">
                                            {{ $settlement->settlement_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-bold text-dark dark:text-white-light">
                                            {{ $settlement->officeAccount->account_name ?? 'Account #' . $settlement->office_account_id }}
                                        </div>
                                        @if($settlement->journalEntry)
                                            <div class="text-[10px] text-gray-400">
                                                Ref: {{ $settlement->journalEntry->reference_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ ucfirst(str_replace('_', ' ', $settlement->payment_method)) }}</div>
                                        @if($settlement->reference_no)
                                            <div class="text-[10px] text-gray-500">{{ $settlement->reference_no }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right font-bold text-dark dark:text-white-light">
                                        ৳{{ number_format($settlement->amount_bdt, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">
                                        No settlements recorded yet for this commission deal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Settle Payment Modal (Compliant with AGENTS.md Rule 1) -->
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
                            <h5 class="text-base font-bold text-dark dark:text-white-light">
                                {{ $partnerCommission->direction === 'receivable' ? 'Receive Commission Inflow' : 'Pay Partner Commission Outflow' }}
                            </h5>
                            <p class="text-xs text-gray-500">Record transaction into Office Account and post to General Ledger.</p>
                        </div>
                        <button type="button" @click="settleModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.partner-commissions.settle', $partnerCommission->id) }}" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-[#0e1726] text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Agreed:</span>
                                <span class="font-bold text-dark dark:text-white-light">৳{{ number_format($partnerCommission->total_amount_bdt, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Remaining Due:</span>
                                <span class="font-bold text-danger">৳{{ number_format($partnerCommission->due_amount_bdt, 2) }}</span>
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
                                <input type="number" step="0.01" min="1" max="{{ max(1, $partnerCommission->due_amount_bdt) }}" name="amount_bdt" value="{{ $partnerCommission->due_amount_bdt }}" required class="form-input font-bold" />
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
                            <button type="submit" class="btn btn-success">
                                {{ $partnerCommission->direction === 'receivable' ? 'Record Inflow' : 'Record Outflow' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function partnerCommissionShowHandler() {
    return {
        settleModalOpen: false,
    };
}
</script>
@endpush

@endsection
