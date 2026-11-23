@extends('admin.layouts.master')

@section('title', 'Accounting Periods')

@section('content')

<div x-data="periodModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Accounting Periods</h2>
        <button type="button" @click="openCreateModal()" class="btn btn-primary">Setup New Period</button>
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 16px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $periods->count() }} Periods
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Open: {{ $openCount ?? 0 }}
                </span>
            </div>
        </div>

        <div class="table-responsive" style="min-height: 280px;">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Range</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        <tr>
                            <td class="font-semibold">{{ $period->name }}</td>
                            <td class="text-xs">
                                {{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="badge badge-outline-primary uppercase text-[10px]">
                                    {{ str_replace('_', ' ', $period->type) }}
                                </span>
                            </td>
                            <td>
                                @if ($period->status == 'open')
                                    <span class="badge badge-outline-success">Open</span>
                                @else
                                    <span class="badge badge-outline-danger">Closed</span>
                                @endif
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
                                            <button type="button"
                                                @click="toggle(); openEditModal({
                                                    id: {{ $period->id }},
                                                    name: @js($period->name),
                                                    type: @js($period->type),
                                                    start_date: @js($period->start_date->format('Y-m-d')),
                                                    end_date: @js($period->end_date->format('Y-m-d')),
                                                    remarks: @js($period->remarks ?? '')
                                                })"
                                                class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.accounting-periods.update', $period) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $period->status == 'open' ? 'closed' : 'open' }}">
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-warning hover:!bg-warning/10 hover:!text-warning">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-warning" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                    </svg>
                                                    {{ $period->status == 'open' ? 'Close Period' : 'Re-open Period' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.accounting-periods.destroy', $period->id) }}"
                                                method="POST" class="m-0" onsubmit="return confirm('Delete this accounting period?');">
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
                            <td colspan="5" class="text-center text-gray-400 py-8">No accounting periods defined yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
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
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Accounting Period' : 'Setup New Period'"></h5>
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

                        <div class="p-5 space-y-4">

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Period Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-input" x-model="form.name" required placeholder="e.g. FY 2026-27">
                            </div>
                            <div class="form-group">
                                <label>Period Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" x-model="form.type" required>
                                    <option value="fiscal_year">Fiscal Year</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-input" x-model="form.start_date" required>
                                </div>
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-input" x-model="form.end_date" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Remarks (Optional)</label>
                                <textarea name="remarks" rows="3" class="form-textarea" x-model="form.remarks" placeholder="Notes about this period..."></textarea>
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-5 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update Period' : 'Create Period'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function periodModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.accounting-periods.store') }}',
        form: {
            name: @js(old('name', '')),
            type: @js(old('type', 'fiscal_year')),
            start_date: @js(old('start_date', '')),
            end_date: @js(old('end_date', '')),
            remarks: @js(old('remarks', '')),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.accounting-periods.store') }}';
            this.form = { name: '', type: 'fiscal_year', start_date: '', end_date: '', remarks: '' };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/accounting-periods') }}/' + item.id;
            this.form = {
                name: item.name,
                type: item.type,
                start_date: item.start_date,
                end_date: item.end_date,
                remarks: item.remarks,
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
