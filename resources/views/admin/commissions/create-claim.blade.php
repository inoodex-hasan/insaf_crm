@extends('admin.layouts.master')

@section('title', 'Submit Commission Claim')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Submit Commission Claim</h5>
            <a href="{{ route('my-commissions.claimable') }}" class="btn btn-secondary gap-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to List
            </a>
        </div>

        {{-- Application Details --}}
        <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4 mb-6">
            <h6 class="font-semibold mb-3">Application Details</h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-500 text-sm">Application ID:</span>
                    <div class="font-medium">{{ $application->application_id }}</div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Student:</span>
                    <div class="font-medium">{{ $application->student->full_name ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">University:</span>
                    <div class="font-medium">{{ $application->university->name ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Course:</span>
                    <div class="font-medium">{{ $application->course->name ?? '-' }}</div>
                </div>
                <!-- <div>
                    <span class="text-gray-500 text-sm">Tuition Fee:</span>
                    <div class="font-medium">BDT {{ number_format($application->tuition_fee, 2) }}</div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Total Fee:</span>
                    <div class="font-medium">BDT {{ number_format($application->total_fee, 2) }}</div>
                </div> -->
            </div>
        </div>

        {{-- Rejected Claim Notice --}}
        @if(isset($rejectedClaim) && $rejectedClaim)
            <div class="bg-danger/10 border border-danger/20 rounded-md p-4 mb-6">
                <div class="flex items-start gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger shrink-0 mt-0.5">
                        <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <div class="font-medium text-danger">Previous Claim Was Rejected</div>
                        <div class="text-sm mt-1">
                            <span class="text-gray-600">Rejected by:</span> {{ $rejectedClaim->reviewer->name ?? 'Accountant' }} on {{ $rejectedClaim->reviewed_at?->format('M d, Y') }}<br>
                            <span class="text-gray-600">Reason:</span> {{ $rejectedClaim->review_notes }}
                        </div>
                        <div class="text-sm mt-2 text-gray-600">
                            You can submit a new claim with adjusted amount below.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Claim Form --}}
        <form action="{{ route('my-commissions.store-claim', $application) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- <div>
                    <label class="block text-sm font-medium mb-2">Commission Percentage (%)</label>
                    <input type="number" name="percentage" id="percentage" 
                        value="{{ old('percentage', $rejectedClaim->percentage ?? '') }}" 
                        min="0" max="100" step="0.01" 
                        class="form-input w-full @error('percentage') border-danger @enderror"
                        placeholder="e.g. 5.00">
                    @error('percentage')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                    <div class="text-sm text-gray-500 mt-1">
                        Suggested: 2% - 10% based on your role
                    </div>
                </div> -->

                <div>
                    <label class="block text-sm font-medium mb-2">Proposed Amount (BDT)</label>
                    <input type="number" name="proposed_amount" id="proposed_amount" 
                        value="{{ old('proposed_amount', $rejectedClaim->proposed_amount ?? '') }}" 
                        min="0" step="0.01" 
                        class="form-input w-full @error('proposed_amount') border-danger @enderror"
                        placeholder="e.g. 5000.00" required>
                    @error('proposed_amount')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                    <!-- <div class="text-sm text-gray-500 mt-1">
                        Based on tuition fee: BDT {{ number_format($application->tuition_fee, 2) }}
                    </div> -->
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Notes / Justification</label>
                <textarea name="claim_notes" rows="4" 
                    class="form-input w-full @error('claim_notes') border-danger @enderror"
                    placeholder="Explain your role in this application and why you deserve this commission...">{{ old('claim_notes', $rejectedClaim->claim_notes ?? '') }}</textarea>
                @error('claim_notes')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn btn-primary">
                    Submit Claim for Review
                </button>
                <a href="{{ route('my-commissions.claimable') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

            <div class="mt-4 text-sm text-gray-500">
                <p><strong>Note:</strong> Your claim will be reviewed by the accountant. The final approved amount may differ from your proposed amount based on company policy.</p>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const percentageInput = document.getElementById('percentage');
        const amountInput = document.getElementById('proposed_amount');
        const tuitionFee = {{ $application->tuition_fee ?? 0 }};

        // Auto-calculate amount when percentage changes
        percentageInput.addEventListener('input', function() {
            const percentage = parseFloat(this.value) || 0;
            if (tuitionFee > 0 && percentage > 0) {
                const amount = (tuitionFee * percentage) / 100;
                amountInput.value = amount.toFixed(2);
            }
        });

        // Auto-calculate percentage when amount changes
        amountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            if (tuitionFee > 0 && amount > 0) {
                const percentage = (amount / tuitionFee) * 100;
                percentageInput.value = percentage.toFixed(2);
            }
        });
    });
</script> -->
@endpush
