@extends('admin.layouts.master')

@section('title', 'Application Details - ' . $application->application_id)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Application: {{ $application->application_id }}</h2>
        <div class="flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-secondary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back
            </a>
            @can('*application')
                <a href="{{ route('admin.applications.edit', $application) }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit Application
                </a>
            @endcan
        </div>
    </div>

    {{-- Student Info Card --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Student Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Name</label>
                @if($application->student)
                    <p class="font-semibold text-lg">
                        <a href="{{ route('admin.students.show', $application->student->id) }}" class="text-primary hover:underline">
                            {{ $application->student->full_name }}
                        </a>
                    </p>
                @else
                    <p class="font-semibold text-lg text-danger italic">N/A</p>
                @endif
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Email</label>
                <p class="font-medium">{{ $application->student->email ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Phone</label>
                <p class="font-medium">{{ $application->student->phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Academic Info Card --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Academic Information</h5>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Country</label>
                <p class="font-medium">{{ $application->university?->country?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">University</label>
                <p class="font-medium">{{ $application->university?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Course</label>
                <p class="font-medium">{{ $application->course?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Intake</label>
                <p class="font-medium">{{ $application->intake?->intake_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Application Status & Notes --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Application Status</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Current Status</label>
                <p>
                    <span class="badge badge-outline-{{ $application->status === 'enrolled' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }} text-sm">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>
                </p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Final Status</label>
                <p>
                    <span class="badge badge-outline-{{ $application->final_status === 'completed' ? 'success' : ($application->final_status === 'cancelled' ? 'danger' : 'info') }} text-sm">
                        {{ ucfirst(str_replace('_', ' ', $application->final_status ?? 'Pending')) }}
                    </span>
                </p>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-gray-500 dark:text-gray-400">Notes</label>
                <p class="text-gray-700 dark:text-gray-300">{{ $application->notes ?: 'No notes added.' }}</p>
            </div>
        </div>
    </div>

    {{-- Application Tracking --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Application Tracking</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            {{-- Offer Letter --}}
            <div class="border rounded-lg p-4 {{ $application->offer_letter_received ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-4 h-4 rounded-full {{ $application->offer_letter_received ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="font-semibold">Offer Letter Received</span>
                </div>
                @if($application->offer_letter_received && $application->offer_letter_received_date)
                    <p class="text-sm text-gray-600">Date: {{ $application->offer_letter_received_date->format('M d, Y') }}</p>
                @endif
            </div>

            {{-- VFS Appointment --}}
            <div class="border rounded-lg p-4 {{ $application->vfs_appointment ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-4 h-4 rounded-full {{ $application->vfs_appointment ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="font-semibold">VFS Appointment</span>
                </div>
                @if($application->vfs_appointment && $application->vfs_appointment_date)
                    <p class="text-sm text-gray-600">Date: {{ $application->vfs_appointment_date->format('M d, Y') }}</p>
                @endif
            </div>

            {{-- File Submission --}}
            <div class="border rounded-lg p-4 {{ $application->file_submission ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-4 h-4 rounded-full {{ $application->file_submission ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="font-semibold">File Submission</span>
                </div>
                @if($application->file_submission && $application->file_submission_date)
                    <p class="text-sm text-gray-600">Date: {{ $application->file_submission_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Visa Status</label>
                <p>
                    <span class="badge badge-outline-{{ $application->visa_status === 'approved' ? 'success' : ($application->visa_status === 'rejected' ? 'danger' : ($application->visa_status === 'pending' ? 'warning' : 'secondary')) }}">
                        {{ ucfirst(str_replace('_', ' ', $application->visa_status ?? 'Not Applied')) }}
                    </span>
                </p>
            </div>
            @if($application->visa_decision_date)
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-400">Visa Decision Date</label>
                    <p class="font-medium">{{ $application->visa_decision_date->format('M d, Y') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Payment Status --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Payment Status</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <div class="border rounded-lg p-3 {{ $application->security_deposit_status ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $application->security_deposit_status ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="text-sm font-medium">Security Deposit</span>
                </div>
            </div>
            <div class="border rounded-lg p-3 {{ $application->cvu_fee_status ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $application->cvu_fee_status ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="text-sm font-medium">CVU Fee</span>
                </div>
            </div>
            <div class="border rounded-lg p-3 {{ $application->admission_fee_status ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $application->admission_fee_status ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="text-sm font-medium">Admission Fee</span>
                </div>
            </div>
            <div class="border rounded-lg p-3 {{ $application->final_payment_status ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $application->final_payment_status ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="text-sm font-medium">Final Payment</span>
                </div>
            </div>
            <div class="border rounded-lg p-3 {{ $application->emgs_payment_status ? 'border-success bg-success/5' : 'border-gray-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $application->emgs_payment_status ? 'bg-success' : 'bg-gray-300' }}"></span>
                    <span class="text-sm font-medium">EMGS Payment</span>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">EMGS Score</label>
                <p class="font-medium">{{ $application->emgs_score ? $application->emgs_score : 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Tuition Fee Status</label>
                <p>
                    <span class="badge badge-outline-{{ $application->tuition_fee_status === 'paid' ? 'success' : ($application->tuition_fee_status === 'partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($application->tuition_fee_status ?? 'Pending') }}
                    </span>
                </p>
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Service Charge Status</label>
                <p>
                    <span class="badge badge-outline-{{ $application->service_charge_status === 'paid' ? 'success' : ($application->service_charge_status === 'partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($application->service_charge_status ?? 'Pending') }}
                    </span>
                </p>
            </div>
            <!-- <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">EMGS Score</label>
                <p class="font-medium">{{ $application->emgs_score ? $application->emgs_score : 'N/A' }}</p>
            </div> -->
        </div>
    </div>

    {{-- Internal Notes --}}
    @if($application->internal_notes)
        <div class="panel mt-6 bg-yellow-50 dark:bg-yellow-900/10">
            <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-2">Internal Notes</h5>
            <p class="text-gray-700 dark:text-gray-300 italic">{{ $application->internal_notes }}</p>
        </div>
    @endif

    {{-- Payment History --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Payment History</h5>
        <div class="table-responsive">
            <table class="table-hover w-full">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Collected By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($application->payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</td>
                            <td class="capitalize">{{ $payment->payment_type }}</td>
                            <td>BDT {{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $payment->payment_status === 'completed' ? 'badge-outline-success' : 'badge-outline-warning' }}">
                                    {{ $payment->payment_status }}
                                </span>
                            </td>
                            <td>{{ $payment->collector->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No payment history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Commission Info --}}
    @if($application->commissions->count() > 0)
        <div class="panel mt-6">
            <h5 class="text-lg font-semibold dark:text-white-light uppercase mb-4">Commission</h5>
            @foreach($application->commissions as $commission)
                <div class="flex items-center justify-between p-4 border rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                        <div>
                            <label class="text-xs text-gray-500">Commission To</label>
                            <p class="font-medium">{{ $commission->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Amount</label>
                            <p class="font-medium">BDT {{ number_format($commission->amount, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Status</label>
                            <p>
                                <span class="badge {{ $commission->status === 'paid' ? 'badge-outline-success' : 'badge-outline-warning' }}">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Documents Summary --}}
    <div class="panel mt-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="text-lg font-semibold dark:text-white-light uppercase">Documents (SOP, CV, Cover Letter)</h5>
            @can('*digital_marketing')
                <a href="{{ route('admin.marketing.documents.index', ['application_id' => $application->id]) }}" class="btn btn-sm btn-outline-primary">
                    Manage Documents
                </a>
            @endcan
        </div>

        @php
            $docTypes = ['sop' => 'SOP', 'cv' => 'CV', 'cl' => 'Cover Letter'];
            $docStatus = [];
            foreach ($application->documents as $doc) {
                $docStatus[$doc->document_type] = $doc;
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($docTypes as $type => $label)
                @php
                    $doc = $docStatus[$type] ?? null;
                    $status = $doc ? $doc->getStatusLabel() : 'Not Set';
                    $statusClass = $doc ? $doc->getStatusClass() : 'badge-outline-dark';
                @endphp
                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <span class="font-semibold">{{ $label }}</span>
                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Meta Info --}}
    <div class="panel mt-6">
        <div class="text-sm text-gray-500">
            <p>Created by: <strong>{{ $application->creator->name ?? 'System' }}</strong> on {{ $application->created_at->format('M d, Y') }}</p>
            @if($application->updated_at != $application->created_at)
                <p class="mt-1">Last updated: {{ $application->updated_at->format('M d, Y') }}</p>
            @endif
        </div>
    </div>
@endsection
