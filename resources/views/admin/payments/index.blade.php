@extends('admin.layouts.master')

@section('title', 'Payments')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <h2 class="text-xl font-semibold uppercase">Payments</h2>
    @can('*accountant')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.payments.report', array_merge(request()->all(), ['output' => 'preview'])) }}"
            target="_blank" class="btn btn-outline-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            Preview
        </a>
        <a href="{{ route('admin.payments.report', array_merge(request()->all(), ['output' => 'download'])) }}"
            class="btn btn-outline-success gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download
        </a>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Payment
        </a>
    </div>
    @endcan
</div>

<div class="panel mt-6">
    <!-- Toolbar: KPI summary & Search -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                Total: {{ $totalCount ?? $payments->total() }} Payments
            </span>
            <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                Completed: {{ $completedCount ?? 0 }}
            </span>
        </div>

        <form action="{{ route('admin.payments.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
            <select name="payment_type" class="form-select text-xs" style="height: 36px; width: 120px; border-radius: 6px;" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="advance" {{ request('payment_type') == 'advance' ? 'selected' : '' }}>Advance</option>
                <option value="final" {{ request('payment_type') == 'final' ? 'selected' : '' }}>Final</option>
            </select>

            <select name="payment_status" class="form-select text-xs" style="height: 36px; width: 130px; border-radius: 6px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <div style="position: relative; width: 220px;">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search student, receipt..."
                       class="form-input text-xs w-full"
                       style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                <button type="submit"
                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer;"
                        class="text-gray-400 hover:text-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>

            @if(request('search') || request('payment_type') || request('payment_status'))
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
            @endif
        </form>
    </div>

    <div class="datatable">
        <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
            <table class="table-hover w-full table-auto">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Application ID</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <!-- <th>Date</th> -->
                        <th>Status</th>
                        <!-- <th>Collected By</th> -->
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td class="font-semibold text-primary">
                            {{ $payment->application->application_id ?? 'N/A' }}
                        </td>
                        <td>
                            @if ($payment->student)
                                <div class="font-semibold">
                                    <a href="{{ route('admin.students.show', $payment->student->id) }}" class="text-primary hover:underline">
                                        {{ $payment->student->first_name }} {{ $payment->student->last_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-white-dark">{{ $payment->student->phone }}</div>
                            @else
                                <span class="text-danger italic">N/A</span>
                            @endif
                        </td>
                        <td class="capitalize">{{ $payment->payment_type }}</td>
                        <td>BDT {{ number_format($payment->amount, 2) }}</td>
                        <!-- <td>{{ optional($payment->payment_date)->format('M d, Y') }}</td> -->
                        <td>
                            <span
                                class="badge {{ $payment->payment_status === 'completed' ? 'badge-outline-success' : 'badge-outline-warning' }}">
                                {{ $payment->payment_status }}
                            </span>
                        </td>
                        <!-- <td>{{ $payment->collector->name ?? '-' }}</td> -->
                        <td class="text-center">
                            <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                    <li>
                                        <a href="{{ route('admin.payments.download-invoice', $payment->id) }}" @click="toggle">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                            Invoice
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.payments.edit', $payment->id) }}" @click="toggle">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this payment?');" class="m-0">
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
                        <td colspan="7" class="text-center">No data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection