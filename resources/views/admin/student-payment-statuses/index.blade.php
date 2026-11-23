@extends('admin.layouts.master')

@section('title', 'Student Payment Statuses')

@section('content')
<div x-data="paymentStatusManager()">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold uppercase">Student Payment Statuses</h2>
            <p class="text-xs text-white-dark mt-1">
                Configure payment pipeline milestones, badge colors, and dynamic dropdown options for student files.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.student-search.index') }}" class="btn btn-outline-primary gap-1.5 text-xs font-semibold">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Search Students
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2 text-xs font-semibold">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Status
            </button>
        </div>
    </div>

    <!-- Main Panel -->
    <div class="panel">
        
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount }} Statuses
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Active: {{ $activeCount }}
                </span>
            </div>

            <form action="{{ route('admin.student-payment-statuses.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search status or code..."
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
                @if(request('search'))
                    <a href="{{ route('admin.student-payment-statuses.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Data Table -->
        <div class="table-responsive overflow-x-auto min-h-[280px]">
            <table class="table-hover w-full table-auto text-left text-sm">
                <thead>
                    <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-xs uppercase text-gray-500 font-bold bg-[#fbfbfb] dark:bg-[#121e32]">
                        <th class="py-3 px-3 w-16 text-center">Order</th>
                        <th class="py-3 px-4">Status Name</th>
                        <th class="py-3 px-4">Unique Code</th>
                        <th class="py-3 px-4">Badge Preview</th>
                        <th class="py-3 px-4 text-center">Active Status</th>
                        <th class="py-3 px-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
                    @forelse($statuses as $status)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1a2941] transition-colors">
                            <td class="py-3 px-3 text-center text-xs text-gray-400 font-mono">
                                #{{ $status->sort_order }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ $status->name }}
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-gray-500 dark:text-gray-400">
                                <code>{{ $status->code }}</code>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge {{ $status->badge_class }} text-xs font-semibold px-2.5 py-1 rounded-md">
                                    {{ $status->name }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.student-payment-statuses.toggle-status', $status) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    <button type="submit"
                                            title="Click to toggle status"
                                            class="badge cursor-pointer {{ $status->is_active ? 'bg-success/15 text-success hover:bg-success/25' : 'bg-danger/15 text-danger hover:bg-danger/25' }} transition text-xs font-semibold px-2.5 py-1 rounded-full">
                                        {{ $status->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
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
                                                    class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:hover:bg-dark/40"
                                                    @click="toggle(); openEditModal({{ json_encode($status) }})">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.student-payment-statuses.destroy', $status->id) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this payment status?');" class="m-0">
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
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                No payment statuses found. Click "Add New Status" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($statuses->hasPages())
            <div class="mt-4 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                {{ $statuses->links() }}
            </div>
        @endif

    </div>

    <!-- Create / Edit Modal (Teleported to #modal-portal per AGENTS.md) -->
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
                     class="w-full max-w-md rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Payment Status' : 'Add New Payment Status'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Status Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       x-model="formData.name"
                                       required
                                       class="form-input text-xs"
                                       placeholder="e.g. Deposit Received, Waived">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Unique Code <span class="text-[10px] text-slate-400">(Auto-generated if left blank)</span>
                                </label>
                                <input type="text"
                                       name="code"
                                       x-model="formData.code"
                                       class="form-input text-xs"
                                       placeholder="e.g. deposit_received">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Badge Color</label>
                                    <select name="color" x-model="formData.color" class="form-select text-xs">
                                        <option value="info">Info (Blue)</option>
                                        <option value="success">Success (Green)</option>
                                        <option value="warning">Warning (Amber)</option>
                                        <option value="danger">Danger (Red)</option>
                                        <option value="secondary">Secondary (Gray)</option>
                                        <option value="primary">Primary (Indigo)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sort Order</label>
                                    <input type="number"
                                           name="sort_order"
                                           x-model="formData.sort_order"
                                           class="form-input text-xs"
                                           placeholder="1">
                                </div>
                            </div>

                            <template x-if="isEdit">
                                <div class="flex items-center gap-2 pt-1">
                                    <input type="checkbox"
                                           name="is_active"
                                           id="is_active_toggle"
                                           value="1"
                                           :checked="formData.is_active"
                                           class="form-checkbox text-primary rounded">
                                    <label for="is_active_toggle" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer mb-0">
                                        Status is Active (Visible in Dropdowns)
                                    </label>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger btn-sm text-xs">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs">
                                <span x-text="isEdit ? 'Update Status' : 'Save Status'"></span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function paymentStatusManager() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('admin.student-payment-statuses.store') }}',
        formData: {
            id: null,
            name: '',
            code: '',
            color: 'info',
            sort_order: '',
            is_active: true
        },
        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.student-payment-statuses.store') }}';
            this.formData = {
                id: null,
                name: '',
                code: '',
                color: 'info',
                sort_order: '',
                is_active: true
            };
            this.modalOpen = true;
        },
        openEditModal(status) {
            this.isEdit = true;
            this.formAction = '{{ url('dashboard/student-payment-statuses') }}/' + status.id;
            this.formData = {
                id: status.id,
                name: status.name,
                code: status.code,
                color: status.color || 'info',
                sort_order: status.sort_order || 0,
                is_active: !!status.is_active
            };
            this.modalOpen = true;
        }
    };
}
</script>
@endpush
@endsection
