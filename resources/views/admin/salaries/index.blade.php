@extends('admin.layouts.master')

@section('title', 'Salary Management')

@section('content')
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="panel">
            <div class="text-sm text-white-dark">Total Salaries</div>
            <div class="mt-2 text-2xl font-bold">{{ number_format($stats['total_salaries'], 2) }}</div>
        </div>
        <div class="panel">
            <div class="text-sm text-white-dark">Total Paid</div>
            <div class="mt-2 text-2xl font-bold text-success">{{ number_format($stats['total_paid'], 2) }}</div>
        </div>
        <div class="panel">
            <div class="text-sm text-white-dark">Pending Amount</div>
            <div class="mt-2 text-2xl font-bold text-danger">{{ number_format($stats['total_pending'], 2) }}</div>
        </div>
        <div class="panel">
            <div class="text-sm text-white-dark">Partial Due</div>
            <div class="mt-2 text-2xl font-bold text-warning">{{ number_format($stats['total_partial'], 2) }}</div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Salary Management</h2>
        <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
            {{-- <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Create Salary
            </a> --}}
            <button type="button" onclick="showBulkPayModal()" class="btn btn-success gap-2" id="bulkPayBtn"
                style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M12 2v20M2 12h20"></path>
                </svg>
                Pay All Selected
            </button>
            <a href="{{ route('admin.salaries.export-excel', ['month' => $selectedMonth]) }}"
                class="btn btn-outline-success gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14,2 14,8 20,8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10,9 9,9 8,9" />
                </svg>
                Export to Excel
            </a>
        </div>
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.salaries.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search employee, month, notes..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <select name="status" class="form-select w-full md:w-36 pr-10">
                        <option value="">Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <input type="month" name="month" value="{{ $selectedMonth }}"
                        class="form-input w-full md:w-44" />
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="overflow-x-auto">
                <form id="bulkPayForm">
                    <table class="table-hover w-full table-auto">
                        <thead>
                            <tr>
                                <th style="width: 30px;"><input type="checkbox" id="selectAll"
                                        onchange="toggleSelectAll(this)"></th>
                                <th>Employee</th>
                                <th>Month</th>
                                <th>Basic</th>
                                <th>Net Salary</th>
                                <th>Paid Amount</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salaries as $salary)
                                <tr>
                                    <td>
                                        @if ($salary->payment_status !== 'paid')
                                            <input type="checkbox" name="salary_ids" value="{{ $salary->id }}"
                                                class="salary-checkbox" onchange="updateBulkPayButton()">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ $salary->employee_name }}</div>
                                        <div class="text-xs text-white-dark">
                                            {{ $salary->user->email ?? 'No linked account' }}
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('M Y') }}</td>
                                    <td>{{ number_format($salary->basic_salary, 2) }}</td>
                                    <td class="font-bold text-primary">{{ number_format($salary->net_salary, 2) }}</td>
                                    <td class="font-semibold">{{ number_format($salary->paid_amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge badge-outline-{{ $salary->status_color }}">{{ $salary->status_label }}</span>
                                    </td>
                                    <td>{{ $salary->creator->name ?? 'System' }}</td>
                                    <td class="text-center">
                                        <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                            <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                                <li>
                                                    <a href="{{ route('admin.salaries.show', $salary->id) }}" @click="toggle">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                        View
                                                    </a>
                                                </li>
                                                @if ($salary->payment_status !== 'paid')
                                                    <li>
                                                        <a href="{{ route('admin.expenses.create', ['salary_id' => $salary->id]) }}" @click="toggle">
                                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                                            </svg>
                                                            Pay Salary
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.salaries.edit', $salary->id) }}" @click="toggle">
                                                        <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST"
                                                        onsubmit="return confirm('Delete this salary record?');" class="m-0">
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
                                    <td colspan="9" class="text-center">No salary records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="mt-4">
                {{ $salaries->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure button state is updated on page load
            updateBulkPayButton();
        });

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.salary-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateBulkPayButton();
        }

        function updateBulkPayButton() {
            const checkboxes = document.querySelectorAll('.salary-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            const bulkPayBtn = document.getElementById('bulkPayBtn');

            if (bulkPayBtn) {
                bulkPayBtn.style.display = checkedCount > 0 ? 'inline-flex' : 'none';
            }
        }

        function showBulkPayModal() {
            const form = document.getElementById('bulkPayForm');
            if (!form) {
                alert('Form not found');
                return;
            }

            const checkedIds = Array.from(form.querySelectorAll('input[name="salary_ids"]:checked'))
                .map(cb => cb.value);

            if (checkedIds.length === 0) {
                alert('Please select at least one salary');
                return;
            }

            const url = '{{ route('admin.salaries.bulk-pay-form') }}?salary_ids=' + checkedIds.join(',');
            window.location.href = url;
        }
    </script>

@endsection

@section('scripts')
@endsection
