@extends('admin.layouts.master')

@section('title', 'Review Commission Claim')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Review Commission Claim</h5>
            <a href="{{ route('admin.commissions.pending') }}" class="btn btn-outline-primary">Back to Pending</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Claim Details --}}
            <div class="lg:col-span-2">
                {{-- Employee Details --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4 mb-4">
                    <h6 class="font-semibold mb-3">Employee Details</h6>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 text-sm">Name:</span>
                            <div class="font-medium">{{ $commission->user->name }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Email:</span>
                            <div class="font-medium">{{ $commission->user->email }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Role in Application:</span>
                            @php
                                $role = '';
                                if ($commission->application->student->assigned_marketing_id === $commission->user_id) {
                                    $role = 'Marketing';
                                } elseif ($commission->application->student->assigned_consultant_id === $commission->user_id) {
                                    $role = 'Consultant';
                                } elseif ($commission->application->student->assigned_application_id === $commission->user_id) {
                                    $role = 'Application Staff';
                                } else {
                                    $role = 'Creator';
                                }
                            @endphp
                            <div><span class="badge badge-outline-info">{{ $role }}</span></div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Claimed Date:</span>
                            <div class="font-medium">{{ $commission->claimed_at?->format('M d, Y H:i') ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Application Details --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4 mb-4">
                    <h6 class="font-semibold mb-3">Application Details</h6>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 text-sm">Application ID:</span>
                            <div class="font-medium">{{ $commission->application->application_id }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Student:</span>
                            <div class="font-medium">{{ $commission->application->student->full_name ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">University:</span>
                            <div class="font-medium">{{ $commission->application->university->name ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Course:</span>
                            <div class="font-medium">{{ $commission->application->course->name ?? '-' }}</div>
                        </div>
                        <!-- <div>
                            <span class="text-gray-500 text-sm">Tuition Fee:</span>
                            <div class="font-medium">BDT {{ number_format($commission->application->tuition_fee, 2) }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Total Fee:</span>
                            <div class="font-medium">BDT {{ number_format($commission->application->total_fee, 2) }}</div>
                        </div> -->
                    </div>
                </div>

                {{-- Claim Notes --}}
                @if($commission->claim_notes)
                    <div class="bg-info/10 rounded-md p-4 mb-4">
                        <h6 class="font-semibold mb-2">Employee's Justification</h6>
                        <p class="text-gray-700 dark:text-gray-300">{{ $commission->claim_notes }}</p>
                    </div>
                @endif

                {{-- Approve & Reject Side by Side --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Approval Form --}}
                    <form action="{{ route('admin.commissions.approve', $commission) }}" method="POST" class="bg-success/5 border border-success/20 rounded-md p-4">
                        @csrf
                        <h6 class="font-semibold mb-3 text-success">Approve Commission</h6>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Final Amount (BDT)</label>
                            <input type="number" name="amount"
                                value="{{ old('amount', $commission->proposed_amount) }}"
                                min="0" step="0.01"
                                class="form-input w-full"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Review Notes (Optional)</label>
                            <textarea name="review_notes" rows="2" class="form-input w-full"
                                placeholder="Any notes for the employee...">{{ old('review_notes') }}</textarea>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                            <span>Proposed:</span>
                            <span class="font-medium">{{ number_format($commission->proposed_amount, 2) }} BDT</span>
                        </div>

                        <button type="submit" class="btn btn-success w-full">
                            Approve
                        </button>
                    </form>

                    {{-- Rejection Form --}}
                    <form action="{{ route('admin.commissions.reject', $commission) }}" method="POST" class="bg-danger/5 border border-danger/20 rounded-md p-4">
                        @csrf
                        <h6 class="font-semibold mb-3 text-danger">Reject Commission</h6>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="review_notes" rows="3" class="form-input w-full @error('review_notes') border-danger @enderror"
                                placeholder="Explain why this claim is being rejected..." required>{{ old('review_notes') }}</textarea>
                            @error('review_notes')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                            <span>Required for rejection</span>
                        </div>

                        <button type="submit" class="btn btn-danger w-full">
                            Reject
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column: Other Claims on Same Application --}}
            <div class="lg:col-span-1">
                <div class="bg-warning/10 rounded-md p-4">
                    <h6 class="font-semibold mb-3">Other Claims on This Application</h6>
                    
                    @if($otherClaims->count() > 0)
                        <div class="space-y-3">
                            @foreach($otherClaims as $otherClaim)
                                <div class="bg-white dark:bg-gray-700 rounded p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium">{{ $otherClaim->user->name }}</span>
                                        <span class="badge badge-outline-{{ $otherClaim->getStatusBadgeClass() }}">
                                            {{ $otherClaim->getWorkflowStatusLabel() }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        BDT {{ number_format($otherClaim->amount ?? $otherClaim->proposed_amount, 2) }}
                                    </div>
                                    @if($otherClaim->review_notes)
                                        <div class="text-sm text-gray-500 mt-1">
                                            <span class="text-gray-400">Note:</span> {{ \Illuminate\Support\Str::limit($otherClaim->review_notes, 50) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No other claims on this application.</p>
                    @endif
                </div>

                {{-- Quick Stats --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4 mt-4">
                    <h6 class="font-semibold mb-3">Quick Stats</h6>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Claims:</span>
                            <span class="font-medium">{{ $otherClaims->count() + 1 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Commission:</span>
                            <span class="font-medium">
                                BDT {{ number_format($otherClaims->sum(function($c) { return $c->amount ?? $c->proposed_amount; }) + ($commission->amount ?? $commission->proposed_amount), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
