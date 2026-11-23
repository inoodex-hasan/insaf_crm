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
            <form action="{{ route('admin.invoices.index') }}" method="GET" style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                
                <!-- Toolbar: KPI summary & Quick Search / Status -->
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 12px;" class="dark:border-[#1b2e4b]">
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            Total: {{ $totalCount ?? $invoices->total() }} Invoices
                        </span>
                        <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                            Paid: {{ $paidCount ?? 0 }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                        <select name="status" class="form-select text-xs" style="height: 36px; width: 140px; border-radius: 6px;" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial Paid</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>

                        <div style="position: relative; width: 220px;">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search invoice #, student..."
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

                        @if(request('search') || request('status') || request('date_from') || request('date_to'))
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                        @endif
                    </div>
                </div>

                <!-- Secondary Row: Date Range Filter -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                    <span class="text-xs text-gray-500 font-medium">Date Range:</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input text-xs" style="height: 34px; width: 140px; border-radius: 6px;" title="From Date" />
                    <span class="text-xs text-gray-400">to</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input text-xs" style="height: 34px; width: 140px; border-radius: 6px;" title="To Date" />
                    <button type="submit" class="btn btn-primary btn-sm text-xs" style="height: 34px;">Apply</button>
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
                            <td class="text-center">
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        <li>
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.invoices.download-pdf', $invoice) }}" target="_blank" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                    <polyline points="10 9 9 9 8 9"></polyline>
                                                </svg>
                                                Download PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.invoices.edit', $invoice) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST"
                                                class="m-0" onsubmit="return confirm('Delete this invoice?')">
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