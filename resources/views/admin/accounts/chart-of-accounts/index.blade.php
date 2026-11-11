@extends('admin.layouts.master')

@section('title', 'Chart of Accounts')

@section('content')

<div x-data="chartAccountModalHandler()" @open-chart-edit.window="openEditModal($event.detail)">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Chart of Accounts</h2>
        <button type="button" @click="openCreateModal()" class="btn btn-primary">Add Account Head</button>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6">
        {{-- Accounts Hierarchy --}}
        <div class="panel">
            <h5 class="mb-5 text-lg font-semibold text-primary">Ledger Structure</h5>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Head Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            @include('admin.accounts.chart-of-accounts.partials.row', ['account' => $account, 'depth' => 0])
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 py-10">Chart of Accounts is empty. Start adding heads.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Account Head' : 'Define New Account Head'"></h5>
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
                                <label>Account Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" x-model="form.type" required>
                                    <option value="asset">Asset</option>
                                    <option value="liability">Liability</option>
                                    <option value="equity">Equity</option>
                                    <option value="revenue">Revenue</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Parent Head (Optional)</label>
                                <select name="parent_id" class="form-select" x-model="form.parent_id">
                                    <option value="">No Parent (Top Level)</option>
                                    @foreach ($parentAccounts as $parent)
                                        <option value="{{ $parent->id }}">
                                            {{ $parent->code }} - {{ $parent->name }} ({{ ucfirst($parent->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Account Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-input" x-model="form.code" required placeholder="e.g. 1000-01">
                            </div>
                            <div class="form-group">
                                <label>Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-input" x-model="form.name" required placeholder="e.g. Cash in Hand">
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-5 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update Account' : 'Register Account'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function chartAccountModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.chart-of-accounts.store') }}',
        form: {
            type: @js(old('type', 'asset')),
            parent_id: @js(old('parent_id', '')),
            code: @js(old('code', '')),
            name: @js(old('name', '')),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.chart-of-accounts.store') }}';
            this.form = { type: 'asset', parent_id: '', code: '', name: '' };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/chart-of-accounts') }}/' + item.id;
            this.form = {
                type: item.type,
                parent_id: item.parent_id,
                code: item.code,
                name: item.name,
            };
            this.modalOpen = true;
        }
    };
}

// Global listener as fallback
window.addEventListener('open-chart-edit', function(e) {
    const el = document.querySelector('[x-data*="chartAccountModalHandler"]');
    if (el && el._x_dataStack && el._x_dataStack[0]) {
        el._x_dataStack[0].openEditModal(e.detail);
    }
});
</script>

@endsection
