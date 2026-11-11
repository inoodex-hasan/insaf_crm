@extends('admin.layouts.master')

@section('title', 'Expenses')

@section('content')

<div x-data="expenseModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Expenses</h2>
        <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
            <a href="{{ route('admin.expenses.preview', request()->query()) }}" class="btn btn-outline-primary gap-2" target="_blank">
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
            <a href="{{ route('admin.expenses.download', request()->query()) }}" class="btn btn-outline-success gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Record Expense
            </button>
        </div>
    </div>

    <div class="panel mt-6">
        <div class="mb-5">
            <form action="{{ route('admin.expenses.index') }}" method="GET"
                class="flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-[200px] md:max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search description, category..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="w-44">
                    <select name="category" class="form-select pr-10">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}"
                                {{ request('category') == $category->name ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-36">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" placeholder="Start Date" />
                </div>

                <div class="w-36">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" placeholder="End Date" />
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="overflow-x-auto min-h-[280px]">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Recorded By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="font-semibold">{{ $expense->description }}</td>
                                <td><span
                                        class="badge badge-outline-info">{{ $expense->chartOfAccount->name ?? 'General' }}</span>
                                </td>
                                <td class="font-bold text-danger">{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->payment_method ?: '-' }}</td>
                                <td>
                                    <div class="text-xs">
                                        {{ $expense->creator->name ?? 'System' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                            <li>
                                                <a href="{{ route('admin.expenses.download-pdf', $expense->id) }}" @click="toggle">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-success" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                    </svg>
                                                    PDF
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" @click="toggle(); openEditModal({
                                                    id: {{ $expense->id }},
                                                    description: @js($expense->description),
                                                    amount: '{{ $expense->amount }}',
                                                    expense_date: '{{ $expense->expense_date->format('Y-m-d') }}',
                                                    chart_of_account_id: '{{ $expense->chart_of_account_id }}',
                                                    payment_method: '{{ $expense->payment_method }}',
                                                    office_account_id: '{{ $expense->office_account_id ?? '' }}',
                                                    notes: @js($expense->notes ?? '')
                                                })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this expense?');" class="m-0">
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
                                <td colspan="7" class="text-center py-6">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    {{-- Wider Modal for Expense (max-w-2xl) --}}
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="modalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="modalOpen = false"
                     x-show="modalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-2xl rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Expense' : 'Record Expense'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-show="isEdit" value="PUT">

                        <div class="p-6 space-y-4">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Description <span class="text-danger">*</span></label>
                                    <input type="text" name="description" class="form-input" x-model="form.description" required placeholder="e.g. Office Stationery">
                                </div>

                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-input" x-model="form.amount" required placeholder="0.00">
                                </div>

                                <div class="form-group">
                                    <label>Expense Date <span class="text-danger">*</span></label>
                                    <input type="date" name="expense_date" class="form-input" x-model="form.expense_date" required>
                                </div>

                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="chart_of_account_id" class="form-select" x-model="form.chart_of_account_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }} ({{ $category->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" x-model="form.payment_method" required>
                                        <option value="cash">Cash</option>
                                        <option value="credit">Credit</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_banking">Mobile Banking</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Pay From Account</label>
                                    <select name="office_account_id" class="form-select" x-model="form.office_account_id">
                                        <option value="">-- Select Account --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_name }}
                                                ({{ ucfirst($account->account_type) }}{{ $account->provider_name ? ' - ' . $account->provider_name : '' }} - {{ $account->account_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label>Notes</label>
                                    <textarea name="notes" rows="3" class="form-textarea" x-model="form.notes" placeholder="Optional notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-6 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary px-8" x-text="isEdit ? 'Update Expense' : 'Save Expense'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function expenseModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.expenses.store') }}',
        form: {
            description: @js(old('description', '')),
            amount: @js(old('amount', '')),
            expense_date: @js(old('expense_date', date('Y-m-d'))),
            chart_of_account_id: @js(old('chart_of_account_id', '')),
            payment_method: @js(old('payment_method', 'cash')),
            office_account_id: @js(old('office_account_id', '')),
            notes: @js(old('notes', '')),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.expenses.store') }}';
            this.form = {
                description: '',
                amount: '',
                expense_date: '{{ date('Y-m-d') }}',
                chart_of_account_id: '',
                payment_method: 'cash',
                office_account_id: '',
                notes: ''
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/expenses') }}/' + item.id;
            this.form = {
                description: item.description,
                amount: item.amount,
                expense_date: item.expense_date,
                chart_of_account_id: item.chart_of_account_id,
                payment_method: item.payment_method,
                office_account_id: item.office_account_id,
                notes: item.notes
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
