@extends('admin.layouts.master')

@section('title', 'Currencies')

@section('content')

<div x-data="currencyModalHandler()">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold uppercase">Currencies</h2>
        @can('*accountant')
            <button type="button" @click="openCreateModal()" class="btn btn-primary">Add Currency</button>
        @endcan
    </div>

    <div class="panel mt-6">
        <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
            <form action="{{ route('admin.currencies.index') }}" method="GET"
                class="flex flex-1 flex-col gap-5 md:flex-row md:items-center w-full">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search currency name or code..." class="form-input ltr:pr-11 rtl:pl-11" />
                    <button type="submit"
                        class="absolute inset-y-0 flex items-center hover:text-primary ltr:right-4 rtl:left-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.currencies.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Symbol</th>
                    <th>Exchange Rate</th>
                    <th>Status</th>
                    <th>Default</th>
                    <th>Last Updated</th>
                    @can('*accountant')
                        <th class="text-center">Action</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($currencies as $currency)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $currency->name }}</td>
                        <td>{{ $currency->code }}</td>
                        <td>{{ $currency->symbol }}</td>
                        <td>{{ $currency->exchange_rate }}</td>
                        <td>
                            <span class="badge {{ $currency->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $currency->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($currency->is_default)
                                <span class="badge bg-primary">Default</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>{{ $currency->updated_at->format('M d, Y') }}</td>
                        @can('*accountant')
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
                                                id: {{ $currency->id }},
                                                name: @js($currency->name),
                                                code: @js($currency->code),
                                                symbol: @js($currency->symbol),
                                                exchange_rate: @js($currency->exchange_rate),
                                                is_active: {{ $currency->is_active ? 1 : 0 }}
                                            })" class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.currencies.destroy', $currency->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this currency?');" class="m-0">
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
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">No currencies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $currencies->links() }}
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
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Currency' : 'Add Currency'"></h5>
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

                            {{-- Validation Errors --}}
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-input" x-model="form.name" required placeholder="e.g. US Dollar">
                                </div>
                                <div class="form-group">
                                    <label>Code (3 Digits) <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-input" x-model="form.code" required placeholder="e.g. USD" maxlength="3">
                                </div>
                                <div class="form-group">
                                    <label>Symbol <span class="text-danger">*</span></label>
                                    <input type="text" name="symbol" class="form-input" x-model="form.symbol" required placeholder="e.g. $">
                                </div>
                                <div class="form-group">
                                    <label>Exchange Rate <span class="text-danger">*</span></label>
                                    <input type="number" step="0.00000001" name="exchange_rate" class="form-input" x-model="form.exchange_rate" required>
                                </div>
                                <div class="form-group col-span-2">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="is_active" class="form-select" x-model="form.is_active" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] px-5 py-4 dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update Currency' : 'Save Currency'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>

<script>
function currencyModalHandler() {
    return {
        modalOpen: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
        isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
        actionUrl: '{{ route('admin.currencies.store') }}',
        form: {
            name: @js(old('name', '')),
            code: @js(old('code', '')),
            symbol: @js(old('symbol', '')),
            exchange_rate: @js(old('exchange_rate', 1)),
            is_active: @js(old('is_active', 1)),
        },
        openCreateModal() {
            this.isEdit = false;
            this.actionUrl = '{{ route('admin.currencies.store') }}';
            this.form = { name: '', code: '', symbol: '', exchange_rate: 1, is_active: 1 };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.actionUrl = '{{ url('dashboard/currencies') }}/' + item.id;
            this.form = {
                name: item.name,
                code: item.code,
                symbol: item.symbol,
                exchange_rate: item.exchange_rate,
                is_active: item.is_active,
            };
            this.modalOpen = true;
        }
    };
}
</script>

@endsection
