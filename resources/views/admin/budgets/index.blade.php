@extends('admin.layouts.master')

@section('title', 'Budget Allocations')

@section('content')

<div x-data="budgetModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Budget Allocations</h2>
        <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
            <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Set Budget
            </button>
        </div>
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.budgets.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                        class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <select name="period" class="form-select w-full md:w-32 pr-10">
                        <option value="">Period</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.budgets.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>

        <div class="datatable">
            <div class="overflow-x-auto">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Period</th>
                            <th>Dates</th>
                            <th>Recorded By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $budget)
                            <tr>
                                <td class="font-semibold uppercase">{{ $budget->chartOfAccount?->name ?? $budget->category }}</td>
                                <td class="text-primary font-bold">{{ number_format($budget->amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $budget->period == 'yearly' ? 'badge-outline-warning' : 'badge-outline-info' }} uppercase">
                                        {{ $budget->period }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm">
                                        {{ \Carbon\Carbon::parse($budget->start_date)->format('M d, Y') }} -
                                        {{ \Carbon\Carbon::parse($budget->end_date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>{{ $budget->creator->name ?? 'System' }}</td>
                                <td class="text-center">
                                    <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                            <li>
                                                <button type="button" @click="toggle(); openEditModal({
                                                    id: {{ $budget->id }},
                                                    chart_of_account_id: '{{ $budget->chart_of_account_id }}',
                                                    amount: '{{ $budget->amount }}',
                                                    period: '{{ $budget->period }}',
                                                    start_date: '{{ \Carbon\Carbon::parse($budget->start_date)->format('Y-m-d') }}',
                                                    end_date: '{{ \Carbon\Carbon::parse($budget->end_date)->format('Y-m-d') }}',
                                                    notes: @js($budget->notes ?? '')
                                                })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.budgets.destroy', $budget->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this budget allocation?');" class="m-0">
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
                                <td colspan="6" class="text-center py-6">No budgets allocated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $budgets->links() }}
            </div>
        </div>
    </div>

    {{-- Wider Modal for Budget (max-w-2xl) --}}
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Budget Allocation' : 'Allocate Budget'"></h5>
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
                                    <label>Budget Category <span class="text-danger">*</span></label>
                                    <select name="chart_of_account_id" class="form-select" x-model="form.chart_of_account_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Allocated Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-input" step="0.01" x-model="form.amount" placeholder="0.00" required>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label>Budget Period <span class="text-danger">*</span></label>
                                    <select name="period" class="form-select" x-model="form.period" required>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-input" x-model="form.start_date" required>
                                </div>

                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-input" x-model="form.end_date" required>
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
                            <button type="submit" class="btn btn-primary px-8" x-text="isEdit ? 'Update Budget' : 'Save Budget'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function budgetModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.budgets.store') }}',
        form: {
            chart_of_account_id: @js(old('chart_of_account_id', '')),
            amount: @js(old('amount', '')),
            period: @js(old('period', 'monthly')),
            start_date: @js(old('start_date', date('Y-m-01'))),
            end_date: @js(old('end_date', date('Y-m-t'))),
            notes: @js(old('notes', '')),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.budgets.store') }}';
            this.form = {
                chart_of_account_id: '',
                amount: '',
                period: 'monthly',
                start_date: '{{ date('Y-m-01') }}',
                end_date: '{{ date('Y-m-t') }}',
                notes: ''
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/budgets') }}/' + item.id;
            this.form = {
                chart_of_account_id: item.chart_of_account_id,
                amount: item.amount,
                period: item.period,
                start_date: item.start_date,
                end_date: item.end_date,
                notes: item.notes
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
