@extends('admin.layouts.master')

@section('title', 'Commission Management')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Commission Management</h5>
            <div class="flex gap-2">
                <a href="{{ route('admin.commissions.pending') }}" class="btn btn-warning">
                    Pending Review
                    @if($stats['pending_review'] > 0)
                        <span class="ml-2 bg-danger text-white text-xs rounded-full px-2 py-0.5">
                            {{ $stats['pending_review'] }}
                        </span>
                    @endif
                </a>
                <!-- <a href="{{ route('admin.commissions.create') }}" class="btn btn-primary">Add Commission</a> -->
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-info/10 rounded-md p-3 text-center">
                <div class="text-info text-sm">Pending Review</div>
                <div class="text-xl font-bold">{{ $stats['pending_review'] }}</div>
            </div>
            <div class="bg-warning/10 rounded-md p-3 text-center">
                <div class="text-warning text-sm">Claimed</div>
                <div class="text-xl font-bold">{{ $stats['claimed'] }}</div>
            </div>
            <div class="bg-secondary/10 rounded-md p-3 text-center">
                <div class="text-secondary text-sm">Under Review</div>
                <div class="text-xl font-bold">{{ $stats['under_review'] }}</div>
            </div>
            <div class="bg-success/10 rounded-md p-3 text-center">
                <div class="text-success text-sm">Approved</div>
                <div class="text-xl font-bold">{{ $stats['approved'] }}</div>
            </div>
            <div class="bg-danger/10 rounded-md p-3 text-center">
                <div class="text-danger text-sm">Rejected</div>
                <div class="text-xl font-bold">{{ $stats['rejected'] }}</div>
            </div>
            <div class="bg-primary/10 rounded-md p-3 text-center">
                <div class="text-primary text-sm">Paid</div>
                <div class="text-xl font-bold">{{ $stats['paid'] }}</div>
            </div>
        </div>

        <div class="mb-5">
            <form method="GET" class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by employee, student, application..."
                        class="form-input w-full" />
                </div>
                <div class="w-40 shrink-0">
                    <select name="workflow_status" class="form-select w-full text-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('workflow_status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="claimed" {{ request('workflow_status') === 'claimed' ? 'selected' : '' }}>Claimed</option>
                        <option value="under_review" {{ request('workflow_status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ request('workflow_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('workflow_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="paid" {{ request('workflow_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary whitespace-nowrap">Search</button>
            </form>

            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th>Application</th>
                            <th>Student</th>
                            <th>Employee</th>
                            <th>Proposed</th>
                            <th>Final</th>
                            <th>Status</th>
                            <th>Claimed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                            <tr>
                                <td>{{ $commission->application->application_id ?? '-' }}</td>
                                <td>
                                    @if($commission->application?->student)
                                        <a href="{{ route('admin.students.show', $commission->application->student->id) }}" class="text-primary hover:underline font-semibold">
                                            {{ $commission->application->student->full_name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $commission->user->name ?? '-' }}</td>
                                <td>BDT {{ number_format($commission->proposed_amount, 2) }}</td>
                                <td>
                                    @if($commission->amount > 0)
                                        BDT {{ number_format($commission->amount, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline-{{ $commission->getStatusBadgeClass() }}">
                                        {{ $commission->getWorkflowStatusLabel() }}
                                    </span>
                                </td>
                                <td>{{ $commission->claimed_at?->format('M d, Y') ?? '-' }}</td>
                                <td>
                                    <div class="flex gap-2">
                                                <a href="{{ route('admin.commissions.show', $commission) }}" class="btn btn-sm btn-secondary">
                                            View
                                        </a>
                                        @if($commission->canBeReviewed())
                                            <a href="{{ route('admin.commissions.review', $commission) }}" class="btn btn-sm btn-primary">
                                                Review
                                            </a>
                                        @elseif($commission->canBePaid())
                                            <form action="{{ route('admin.commissions.mark-paid', $commission) }}" method="POST" class="flex flex-col gap-2">
                                                @csrf
                                                <textarea name="paid_notes" rows="2" class="form-input text-sm" placeholder="Enter payment remarks..." required></textarea>
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Mark this commission as paid?')">Mark Paid</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.commissions.destroy', $commission) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this commission?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500">No commissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleStatus(commissionId, currentStatus) {
            const newStatus = currentStatus === 'pending' ? 'paid' : 'pending';

            fetch(`{{ url('dashboard/commissions') }}/${commissionId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    </script>
@endpush
