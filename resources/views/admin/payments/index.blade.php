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
    <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
        <form action="{{ route('admin.payments.index') }}" method="GET"
            class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search student name, phone or email..." class="form-input ltr:pr-11 rtl:pl-11" />
                <button type="submit"
                    class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                        <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
            <div class="flex gap-2">
                <select name="payment_type" class="form-select w-auto md:w-auto pr-10">
                    <option value="">Type</option>
                    <option value="advance" {{ request('payment_type')=='advance' ? 'selected' : '' }}>Advance
                    </option>
                    <option value="final" {{ request('payment_type')=='final' ? 'selected' : '' }}>Final</option>
                </select>
                <select name="payment_status" class="form-select w-auto md:w-auto pr-10">
                    <option value="">Status</option>
                    <option value="pending" {{ request('payment_status')=='pending' ? 'selected' : '' }}>Pending
                    </option>
                    <option value="partial" {{ request('payment_status')=='partial' ? 'selected' : '' }}>Partial
                    </option>
                    <option value="completed" {{ request('payment_status')=='completed' ? 'selected' : '' }}>Completed
                    </option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div class="datatable">
        <div class="overflow-x-auto min-h-[280px]">
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
                            <div class="font-semibold">
                                {{ $payment->student->first_name }} {{ $payment->student->last_name }}
                            </div>
                            <div class="text-xs text-white-dark">{{ $payment->student->phone }}</div>
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
                                <ul x-cloak x-show="open" x-transition x-transition.duration.300ms class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
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