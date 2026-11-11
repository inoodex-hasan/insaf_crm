@extends('admin.layouts.master')

@section('title', 'Bank Reconciliations')

@section('content')

<div x-data="reconciliationModalHandler()">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Bank Reconciliations</h2>
        <button type="button" @click="openCreateModal()" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Reconciliation
        </button>
    </div>

    <div class="panel mt-6">
        <div class="table-responsive">
            <table class="table-hover">
                <thead>
                    <tr>
                        <th>Statement Date</th>
                        <th>Account</th>
                        <th class="text-right">Statement Balance</th>
                        <th class="text-right">System Balance</th>
                        <th class="text-right">Difference</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $rec)
                        <tr>
                            <td>{{ $rec->statement_date->format('M d, Y') }}</td>
                            <td class="font-bold text-primary">{{ $rec->account->account_name }}</td>
                            <td class="text-right font-mono">{{ number_format($rec->statement_balance, 2) }}</td>
                            <td class="text-right font-mono">{{ number_format($rec->system_balance, 2) }}</td>
                            <td class="text-right font-mono {{ $rec->difference == 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($rec->difference, 2) }}
                            </td>
                            <td>
                                @if($rec->status == 'closed')
                                    <span class="badge badge-outline-success font-black uppercase text-[10px]">Closed</span>
                                @else
                                    <span class="badge badge-outline-warning font-black uppercase text-[10px]">Open (Draft)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.bank-reconciliations.show', $rec) }}" class="btn btn-sm btn-outline-primary">
                                        {{ $rec->status == 'closed' ? 'View' : 'Continue' }}
                                    </a>
                                    @if($rec->status != 'closed')
                                        <form action="{{ route('admin.bank-reconciliations.destroy', $rec) }}" method="POST" onsubmit="return confirm('Delete this draft?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-12">
                                No bank reconciliations found. Start by clicking "New Reconciliation".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $reconciliations->links() }}
        </div>
    </div>

    {{-- Create Modal --}}
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light">Start Bank Reconciliation</h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <form action="{{ route('admin.bank-reconciliations.store') }}" method="POST">
                        @csrf
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
                                <label for="modal_account_id">Bank / Office Account <span class="text-danger">*</span></label>
                                <select name="account_id" id="modal_account_id" class="form-select" x-model="form.account_id" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="modal_statement_date">Statement Ending Date <span class="text-danger">*</span></label>
                                    <input type="date" name="statement_date" id="modal_statement_date" class="form-input" x-model="form.statement_date" required />
                                </div>
                                <div class="form-group">
                                    <label for="modal_statement_balance">Ending Balance (Bank) <span class="text-danger">*</span></label>
                                    <input type="number" name="statement_balance" id="modal_statement_balance" step="0.01" class="form-input" x-model="form.statement_balance" required placeholder="0.00" />
                                </div>
                            </div>

                            <div class="bg-primary/5 p-4 rounded-lg border border-primary/20">
                                <p class="text-[10px] font-bold uppercase text-primary tracking-widest mb-1">Information:</p>
                                <p class="text-xs text-white-dark leading-relaxed">
                                    Initializing this reconciliation will compare your <strong>Bank Statement Ending Balance</strong> with the <strong>System Balance</strong> of the selected account. On the next screen, you will match individual transactions to eliminate the difference.
                                </p>
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-5 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary">Initialize Reconciliation</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function reconciliationModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        form: {
            account_id: @js(old('account_id', '')),
            statement_date: @js(old('statement_date', date('Y-m-d'))),
            statement_balance: @js(old('statement_balance', '')),
        },
        openCreateModal() {
            this.form = {
                account_id: '',
                statement_date: '{{ date('Y-m-d') }}',
                statement_balance: ''
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
