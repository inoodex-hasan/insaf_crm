@extends('admin.layouts.master')

@section('title', 'Student Invoices')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Billing & Invoices</h2>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary gap-2 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create New Invoice
        </a>
    </div>

    <div class="panel mt-6">
        <div class="mb-5">
            <form action="{{ route('admin.invoices.index') }}" method="GET" class="flex flex-col gap-3 w-full">
                <div style="display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap;">
                    <div class="relative" style="flex: 2; min-width: 200px;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search invoice #, student, university..." class="form-input ltr:pr-11 rtl:pl-11" style="width: 100%;" />
                        <button type="submit"
                            class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                                <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <select name="status" class="form-select" style="flex: 1; min-width: 150px;">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial Paid</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap;">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        placeholder="From Date" class="form-input" style="flex: 1; min-width: 150px;" />
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        placeholder="To Date" class="form-input" style="flex: 1; min-width: 150px;" />

                    <div style="flex: 2; min-width: 100px;"></div>

                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Filter</button>
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-danger" style="white-space: nowrap;">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-hover w-full table-auto">
                <thead>
                    <tr>
                        <th>Issue Date</th>
                        <th>Invoice Number</th>
                        <th>Student Name</th>
                        <th>University</th>
                        <th>Total Amount</th>
                        <!-- <th>Billing Status</th> -->
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="whitespace-nowrap text-xs text-white-dark">{{ $invoice->date->format('M d, Y') }}</td>
                            <td class="font-bold text-primary">
                                <a href="{{ route('admin.invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                            </td>
                            <td class="font-semibold text-sm">
                                @if ($invoice->student)
                                    <a href="{{ route('admin.students.show', $invoice->student->id) }}" class="text-primary hover:underline">
                                        {{ $invoice->student->first_name }} {{ $invoice->student->last_name }}
                                    </a>
                                    <span class="block text-[10px] text-white-dark">{{ $invoice->student->id_number }}</span>
                                @else
                                    <span class="text-danger italic">N/A</span>
                                @endif
                            </td>
                            <td class="text-xs">{{ $invoice->university->name ?? 'N/A' }}</td>
                            <td class="font-black text-dark dark:text-white-light font-mono">
                                {{ number_format($invoice->total_amount, 2) }}</td>
                            <!-- <td>
                                @if($invoice->status == 'paid')
                                    <span class="badge badge-outline-success uppercase text-[10px] font-black">Fully Paid</span>
                                @elseif($invoice->status == 'partial')
                                    <span class="badge badge-outline-warning uppercase text-[10px] font-black">Partial Paid</span>
                                @else
                                    <span class="badge badge-outline-danger uppercase text-[10px] font-black">Unpaid</span>
                                @endif
                            </td> -->
                            <td class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.invoices.show', $invoice) }}"
                                    class="btn btn-sm btn-outline-primary">View</a>

                                <a href="{{ route('admin.invoices.download-pdf', $invoice) }}"
                                    class="btn btn-sm btn-outline-success">PDF</a>

                                <a href="{{ route('admin.invoices.edit', $invoice) }}"
                                    class="btn btn-sm btn-outline-warning">Edit</a>

                                <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-16">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-primary/5 rounded-full mb-3">
                                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1" opacity="0.3">
                                            <path
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold tracking-widest uppercase">No Invoices Found</p>
                                    <p class="text-xs text-white-dark mt-1">Start by billing a student or university.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
@endsection