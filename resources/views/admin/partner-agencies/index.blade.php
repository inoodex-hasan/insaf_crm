@extends('admin.layouts.master')

@section('title', 'Partner Agencies')

@section('content')

<div x-data="partnerAgencyHandler()">
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <div>
            <h2 class="text-xl font-bold uppercase text-dark dark:text-white-light">Partner Agencies</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage B2B agencies for student handover, referral agreements, and commission sharing.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.partner-commissions.index') }}" class="btn btn-outline-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Partner Commissions
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary">
                <svg class="h-4 w-4 ltr:mr-1.5 rtl:ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Partner Agency
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 flex items-center rounded bg-success/10 p-3 text-success">
            <span class="ltr:mr-2 rtl:ml-2">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-5 rounded bg-danger/10 p-3 text-danger">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $agencies->total() }} Agencies
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Active: {{ $activeCount ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.partner-agencies.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search agency, contact..."
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
                    <a href="{{ route('admin.partner-agencies.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>
        <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agency Name</th>
                        <th>Contact Person</th>
                        <th>Contact Info</th>
                        <th>Specialized Countries</th>
                        <th>Total Deals</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agencies as $agency)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-bold text-dark dark:text-white-light">{{ $agency->name }}</div>
                                @if($agency->address)
                                    <div class="text-xs text-gray-500">{{ Str::limit($agency->address, 35) }}</div>
                                @endif
                            </td>
                            <td>{{ $agency->contact_person ?: '—' }}</td>
                            <td>
                                @if($agency->phone)
                                    <div class="text-sm font-semibold">{{ $agency->phone }}</div>
                                @endif
                                @if($agency->email)
                                    <div class="text-xs text-gray-500">{{ $agency->email }}</div>
                                @endif
                                @if(!$agency->phone && !$agency->email)
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                @if($agency->specialized_countries)
                                    <span class="badge bg-primary/10 text-primary border border-primary/20">
                                        {{ $agency->specialized_countries }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary font-bold">
                                    {{ $agency->commissions_count }} {{ Str::plural('deal', $agency->commissions_count) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $agency->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $agency->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
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
                                            <a href="{{ route('admin.partner-commissions.index', ['search' => $agency->name]) }}" class="flex w-full items-center px-4 py-2 text-left text-dark hover:bg-gray-100 dark:text-white-light dark:hover:bg-dark/40">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M12 8v8M8 12h8"/>
                                                </svg>
                                                View Commissions
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" 
                                                    class="flex w-full items-center px-4 py-2 text-left text-primary hover:bg-gray-100 dark:hover:bg-dark/40"
                                                    @click="toggle(); openEditModal(@js($agency))">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit Agency
                                            </button>
                                        </li>
                                        @if($agency->commissions_count == 0)
                                        <li>
                                            <form action="{{ route('admin.partner-agencies.destroy', $agency->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this agency?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-danger hover:!bg-danger/10 hover:!text-danger">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-danger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                No partner agencies found. Click "Add Partner Agency" to register your first partner.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $agencies->links() }}
        </div>
    </div>

    <!-- Modal for Create / Edit Partner Agency (Compliant with AGENTS.md Rule 1) -->
    <template x-teleport="#modal-portal">
        <div x-cloak x-show="modalOpen" class="modal-overlay-full" style="display: none;">
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
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEdit ? 'Edit Partner Agency' : 'Add New Partner Agency'"></h5>
                        <button type="button" @click="modalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form :action="formAction" method="POST" class="p-5 space-y-4">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Agency Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" x-model="form.name" required class="form-input" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Contact Person</label>
                                <input type="text" name="contact_person" x-model="form.contact_person" class="form-input" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Phone / WhatsApp</label>
                                <input type="text" name="phone" x-model="form.phone" class="form-input" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Email Address</label>
                                <input type="email" name="email" x-model="form.email" class="form-input" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Specialized Countries</label>
                                <input type="text" name="specialized_countries" x-model="form.specialized_countries" class="form-input" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Address</label>
                            <input type="text" name="address" x-model="form.address" class="form-input" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Commission Agreement Terms</label>
                            <textarea name="commission_terms" x-model="form.commission_terms" rows="2" class="form-textarea"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-dark dark:text-white-light">Status</label>
                            <select name="status" x-model="form.status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                            <button type="button" @click="modalOpen = false" class="btn btn-outline-danger">Cancel</button>
                            <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update Agency' : 'Save Agency'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function partnerAgencyHandler() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('admin.partner-agencies.store') }}',
        form: {
            id: null,
            name: '',
            contact_person: '',
            phone: '',
            email: '',
            address: '',
            specialized_countries: '',
            commission_terms: '',
            status: 1,
        },
        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.partner-agencies.store') }}';
            this.form = {
                id: null,
                name: '',
                contact_person: '',
                phone: '',
                email: '',
                address: '',
                specialized_countries: '',
                commission_terms: '',
                status: 1,
            };
            this.modalOpen = true;
        },
        openEditModal(agency) {
            this.isEdit = true;
            this.formAction = '{{ url('dashboard/partner-agencies') }}/' + agency.id;
            this.form = {
                id: agency.id,
                name: agency.name || '',
                contact_person: agency.contact_person || '',
                phone: agency.phone || '',
                email: agency.email || '',
                address: agency.address || '',
                specialized_countries: agency.specialized_countries || '',
                commission_terms: agency.commission_terms || '',
                status: agency.status !== undefined ? agency.status : 1,
            };
            this.modalOpen = true;
        }
    };
}
</script>
@endpush

@endsection
