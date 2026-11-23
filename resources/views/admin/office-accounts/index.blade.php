@extends('admin.layouts.master')

@section('title', 'Office Accounts')

@section('content')

<div x-data="officeAccountModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Office Accounts</h2>
        <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
            <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Account
            </button>
        </div>
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $accounts->total() }} Accounts
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Active: {{ $activeCount ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.office-accounts.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <select name="account_type" class="form-select text-xs" style="height: 36px; width: 130px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="bank" {{ request('account_type') == 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="mfs" {{ request('account_type') == 'mfs' ? 'selected' : '' }}>MFS</option>
                    <option value="cash" {{ request('account_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                </select>

                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search account..."
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

                @if(request('search') || request('account_type'))
                    <a href="{{ route('admin.office-accounts.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>

        <div class="datatable">
            <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Account Name</th>
                            <th>Provider</th>
                            <th>Account Number</th>
                            <th>Branch</th>
                            <th>Current Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td class="font-semibold">{{ $account->account_name }}</td>
                                <td>{{ $account->provider_name ?? 'N/A' }}</td>
                                <td class="font-mono">{{ $account->account_number }}</td>
                                <td>{{ $account->branch_name ?? 'N/A' }}</td>
                                @php
                                    $totalCredit = (float) ($account->total_income ?? 0);
                                    $totalDebit = (float) ($account->total_expense ?? 0);
                                    $currentBalance = ($account->opening_balance ?? 0) + $totalDebit - $totalCredit;
                                @endphp
                                <td class="font-semibold">{{ number_format($currentBalance, 2) }}</td>
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
                                                    id: {{ $account->id }},
                                                    account_name: @js($account->account_name),
                                                    account_type: @js($account->account_type),
                                                    provider_name: @js($account->provider_name ?? ''),
                                                    account_number: @js($account->account_number),
                                                    opening_balance: @js($account->opening_balance ?? 0),
                                                    branch_name: @js($account->branch_name ?? ''),
                                                    status: @js($account->status),
                                                    notes: @js($account->notes ?? '')
                                                })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.office-accounts.destroy', $account->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this office account?');" class="m-0">
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
                                <td colspan="6" class="text-center py-6">No office accounts registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>

    {{-- Wider Modal for Office Account (max-w-2xl) --}}
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Office Account' : 'Add Office Account'"></h5>
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
                                    <label>Account Name <span class="text-danger">*</span></label>
                                    <input type="text" name="account_name" class="form-input" x-model="form.account_name" required placeholder="e.g. Main Bank Account">
                                </div>

                                <div class="form-group">
                                    <label>Account Type <span class="text-danger">*</span></label>
                                    <select name="account_type" class="form-select" x-model="form.account_type" required>
                                        <option value="bank">Bank</option>
                                        <option value="mfs">MFS (bKash/Nagad/Rocket)</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Provider Name</label>
                                    <input type="text" name="provider_name" class="form-input" x-model="form.provider_name" placeholder="e.g. City Bank, bKash">
                                </div>

                                <div class="form-group">
                                    <label>Account/Wallet Number <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number" class="form-input" x-model="form.account_number" required placeholder="e.g. 150123456789">
                                </div>

                                <div class="form-group">
                                    <label>Opening Balance</label>
                                    <input type="number" step="0.01" name="opening_balance" class="form-input" x-model="form.opening_balance" placeholder="0.00">
                                </div>

                                <div class="form-group">
                                    <label>Branch Name (For Banks)</label>
                                    <input type="text" name="branch_name" class="form-input" x-model="form.branch_name" placeholder="e.g. Gulshan Branch">
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" x-model="form.status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
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
                            <button type="submit" class="btn btn-primary px-8" x-text="isEdit ? 'Update Account' : 'Save Account'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function officeAccountModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.office-accounts.store') }}',
        form: {
            account_name: @js(old('account_name', '')),
            account_type: @js(old('account_type', 'bank')),
            provider_name: @js(old('provider_name', '')),
            account_number: @js(old('account_number', '')),
            opening_balance: @js(old('opening_balance', 0)),
            branch_name: @js(old('branch_name', '')),
            status: @js(old('status', 'active')),
            notes: @js(old('notes', '')),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.office-accounts.store') }}';
            this.form = {
                account_name: '',
                account_type: 'bank',
                provider_name: '',
                account_number: '',
                opening_balance: 0,
                branch_name: '',
                status: 'active',
                notes: ''
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/office-accounts') }}/' + item.id;
            this.form = {
                account_name: item.account_name,
                account_type: item.account_type,
                provider_name: item.provider_name,
                account_number: item.account_number,
                opening_balance: item.opening_balance,
                branch_name: item.branch_name,
                status: item.status,
                notes: item.notes
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
