@extends('admin.layouts.master')

@section('title', 'Accounting Periods')

@section('content')

<div x-data="periodModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Accounting Periods</h2>
        <button type="button" @click="openCreateModal()" class="btn btn-primary">Setup New Period</button>
    </div>

    <div class="panel mt-6">
        <div class="table-responsive">
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
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Toggle Status --}}
                                    <form action="{{ route('admin.accounting-periods.update', $period) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status"
                                            value="{{ $period->status == 'open' ? 'closed' : 'open' }}">
                                        <button type="submit"
                                            class="btn btn-sm {{ $period->status == 'open' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $period->status == 'open' ? 'Close' : 'Open' }}
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <button type="button"
                                        @click="openEditModal({
                                            id: {{ $period->id }},
                                            name: @js($period->name),
                                            type: @js($period->type),
                                            start_date: @js($period->start_date->format('Y-m-d')),
                                            end_date: @js($period->end_date->format('Y-m-d')),
                                            remarks: @js($period->remarks ?? '')
                                        })"
                                        class="btn btn-sm btn-outline-primary">Edit</button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.accounting-periods.destroy', $period->id) }}"
                                        method="POST" onsubmit="return confirm('Delete this accounting period?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
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
