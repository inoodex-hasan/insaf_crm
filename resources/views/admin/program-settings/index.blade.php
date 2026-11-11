@extends('admin.layouts.master')

@section('title', 'Program Taxonomies & Settings')

@section('content')
<div x-data="programSettingsManager()" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase tracking-wide text-dark dark:text-white-light">
                Program Taxonomies & Settings
            </h2>
            <p class="text-xs text-white-dark mt-1">
                Manage dynamic degree program levels and admission requirements / test waivers used across Search Program and Course applications.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.search-program.index') }}" class="btn btn-outline-success gap-2 text-xs">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Open Search Program
            </a>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary gap-2 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Course List
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="rounded-lg bg-success-light p-3.5 text-success dark:bg-success-dark-light text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light text-xs font-semibold flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="panel p-0 overflow-hidden border border-[#e0e6ed] dark:border-[#1b2e4b] bg-white dark:bg-[#0e1726] rounded-xl">
        <div class="flex items-center border-b border-[#e0e6ed] dark:border-[#1b2e4b] px-6 pt-4 gap-6 bg-slate-50/50 dark:bg-[#121e32]/40">
            <button type="button" 
                    @click="activeTab = 'requirements'" 
                    :class="activeTab === 'requirements' ? 'border-[#22c55e] text-[#22c55e] font-bold' : 'border-transparent text-slate-500 hover:text-dark dark:hover:text-white font-medium'"
                    class="pb-3 border-b-2 text-sm flex items-center gap-2 transition outline-none">
                <span>Admission Requirements & Waivers</span>
                <span class="badge bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 text-[11px] px-2 py-0.5 rounded-full" 
                      x-text="'{{ $requirements->count() }}'"></span>
            </button>

            <button type="button" 
                    @click="activeTab = 'levels'" 
                    :class="activeTab === 'levels' ? 'border-[#22c55e] text-[#22c55e] font-bold' : 'border-transparent text-slate-500 hover:text-dark dark:hover:text-white font-medium'"
                    class="pb-3 border-b-2 text-sm flex items-center gap-2 transition outline-none">
                <span>Program Levels</span>
                <span class="badge bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 text-[11px] px-2 py-0.5 rounded-full" 
                      x-text="'{{ $levels->count() }}'"></span>
            </button>
        </div>

        <div class="p-6">
            
            <!-- TAB 1: REQUIREMENTS -->
            <div x-show="activeTab === 'requirements'" x-cloak>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light">
                            Configured Admission Requirements
                        </h4>
                        <p class="text-xs text-white-dark mt-0.5">
                            These items appear as checkboxes in both Course management and Column 3 of Search Program.
                        </p>
                    </div>
                    <button type="button" 
                            @click="openAddRequirementModal()" 
                            class="btn btn-primary text-xs gap-2 flex items-center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add New Requirement</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-hover w-full text-xs">
                        <thead>
                            <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-slate-600 dark:text-slate-400">
                                <th class="w-12">#</th>
                                <th>Requirement / Test Name</th>
                                <th>Category</th>
                                <th class="text-center">Sort Order</th>
                                <th class="text-center">Status</th>
                                <th class="text-center w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requirements as $req)
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <td class="font-bold text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="font-bold text-dark dark:text-white-light text-sm">
                                        {{ $req->name }}
                                    </td>
                                    <td>
                                        @if($req->category === 'waiver')
                                            <span class="badge" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 700; font-size: 11px; padding: 3px 8px; border-radius: 4px;">
                                                Waiver / Exemption
                                            </span>
                                        @else
                                            <span class="badge" style="background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-weight: 700; font-size: 11px; padding: 3px 8px; border-radius: 4px;">
                                                Standard Test
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center font-medium">{{ $req->sort_order }}</td>
                                    <td class="text-center">
                                        <button type="button" 
                                                @click="toggleRequirementStatus({{ $req->id }}, $el)"
                                                class="badge {{ $req->is_active ? 'badge-outline-success' : 'badge-outline-danger' }} cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1">
                                            {{ $req->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="openEditRequirementModal({{ json_encode($req) }})" 
                                                    class="p-1 rounded text-primary hover:bg-primary/10 transition" title="Edit">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.program-settings.requirements.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete requirement: {{ $req->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded text-danger hover:bg-danger/10 transition" title="Delete">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-white-dark">No requirements configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: PROGRAM LEVELS -->
            <div x-show="activeTab === 'levels'" x-cloak>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-dark dark:text-white-light">
                            Configured Program Levels
                        </h4>
                        <p class="text-xs text-white-dark mt-0.5">
                            Academic degree tiers used in Course management and Column 1 of Search Program.
                        </p>
                    </div>
                    <button type="button" 
                            @click="openAddLevelModal()" 
                            class="btn btn-primary text-xs gap-2 flex items-center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add New Program Level</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-hover w-full text-xs">
                        <thead>
                            <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-slate-600 dark:text-slate-400">
                                <th class="w-12">#</th>
                                <th>Program Level Title</th>
                                <th class="text-center">Sort Order</th>
                                <th class="text-center">Status</th>
                                <th class="text-center w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($levels as $lvl)
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
                                    <td class="font-bold text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="font-bold text-dark dark:text-white-light text-sm">
                                        {{ $lvl->name }}
                                    </td>
                                    <td class="text-center font-medium">{{ $lvl->sort_order }}</td>
                                    <td class="text-center">
                                        <button type="button" 
                                                @click="toggleLevelStatus({{ $lvl->id }}, $el)"
                                                class="badge {{ $lvl->is_active ? 'badge-outline-success' : 'badge-outline-danger' }} cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1">
                                            {{ $lvl->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="openEditLevelModal({{ json_encode($lvl) }})" 
                                                    class="p-1 rounded text-primary hover:bg-primary/10 transition" title="Edit">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.program-settings.levels.destroy', $lvl->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete level: {{ $lvl->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded text-danger hover:bg-danger/10 transition" title="Delete">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-white-dark">No program levels configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL: ADD / EDIT REQUIREMENT (RULE 1 PROTOCOL) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="reqModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="reqModalOpen = false"
                     x-show="reqModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEditReq ? 'Edit Requirement' : 'Add New Requirement'"></h5>
                        <button type="button" @click="reqModalOpen = false" class="p-1 rounded text-slate-400 hover:text-dark dark:hover:text-white">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form :action="reqFormAction" method="POST">
                        @csrf
                        <template x-if="isEditReq">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Requirement / Test Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" x-model="reqForm.name" required class="form-input text-xs" placeholder="e.g. MOI Accepted or German B2">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                                <select name="category" x-model="reqForm.category" class="form-select text-xs">
                                    <option value="test">Standard Test (IELTS, PTE, GRE...)</option>
                                    <option value="waiver">Waiver / Exemption (Without English, Without Maths...)</option>
                                    <option value="other">Other Academic Requirement</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" x-model="reqForm.sort_order" class="form-input text-xs" placeholder="e.g. 15">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="reqModalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-4">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs px-6" x-text="isEditReq ? 'Update Requirement' : 'Save Requirement'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

    <!-- MODAL: ADD / EDIT PROGRAM LEVEL (RULE 1 PROTOCOL) -->
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="levelModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="levelModalOpen = false"
                     x-show="levelModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-5 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <h5 class="text-base font-bold text-dark dark:text-white-light" x-text="isEditLevel ? 'Edit Program Level' : 'Add New Program Level'"></h5>
                        <button type="button" @click="levelModalOpen = false" class="p-1 rounded text-slate-400 hover:text-dark dark:hover:text-white">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <form :action="levelFormAction" method="POST">
                        @csrf
                        <template x-if="isEditLevel">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Program Level Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" x-model="levelForm.name" required class="form-input text-xs" placeholder="e.g. Foundation / Pre-University">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sort Order</label>
                                <input type="number" name="sort_order" x-model="levelForm.sort_order" class="form-input text-xs" placeholder="e.g. 14">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-5 py-3 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="levelModalOpen = false" class="btn btn-outline-danger btn-sm text-xs px-4">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm text-xs px-6" x-text="isEditLevel ? 'Update Program Level' : 'Save Program Level'"></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    function programSettingsManager() {
        return {
            activeTab: '{{ request('tab', 'requirements') }}',

            // Requirement Modal State
            reqModalOpen: false,
            isEditReq: false,
            reqFormAction: '{{ route('admin.program-settings.requirements.store') }}',
            reqForm: {
                name: '',
                code: '',
                category: 'test',
                sort_order: ''
            },

            openAddRequirementModal() {
                this.isEditReq = false;
                this.reqFormAction = '{{ route('admin.program-settings.requirements.store') }}';
                this.reqForm = { name: '', code: '', category: 'test', sort_order: '' };
                this.reqModalOpen = true;
            },

            openEditRequirementModal(req) {
                this.isEditReq = true;
                this.reqFormAction = '{{ url('dashboard/program-settings/requirements') }}/' + req.id;
                this.reqForm = {
                    name: req.name,
                    code: req.code,
                    category: req.category || 'test',
                    sort_order: req.sort_order
                };
                this.reqModalOpen = true;
            },

            async toggleRequirementStatus(id, btnEl) {
                try {
                    let response = await fetch('{{ url('dashboard/program-settings/requirements') }}/' + id + '/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    let data = await response.json();
                    if (data.success) {
                        btnEl.textContent = data.is_active ? 'Active' : 'Inactive';
                        btnEl.className = data.is_active 
                            ? 'badge badge-outline-success cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1'
                            : 'badge badge-outline-danger cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1';
                    }
                } catch (e) {
                    console.error('Toggle failed', e);
                }
            },

            // Level Modal State
            levelModalOpen: false,
            isEditLevel: false,
            levelFormAction: '{{ route('admin.program-settings.levels.store') }}',
            levelForm: {
                name: '',
                code: '',
                sort_order: ''
            },

            openAddLevelModal() {
                this.isEditLevel = false;
                this.levelFormAction = '{{ route('admin.program-settings.levels.store') }}';
                this.levelForm = { name: '', code: '', sort_order: '' };
                this.levelModalOpen = true;
            },

            openEditLevelModal(lvl) {
                this.isEditLevel = true;
                this.levelFormAction = '{{ url('dashboard/program-settings/levels') }}/' + lvl.id;
                this.levelForm = {
                    name: lvl.name,
                    code: lvl.code,
                    sort_order: lvl.sort_order
                };
                this.levelModalOpen = true;
            },

            async toggleLevelStatus(id, btnEl) {
                try {
                    let response = await fetch('{{ url('dashboard/program-settings/levels') }}/' + id + '/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    let data = await response.json();
                    if (data.success) {
                        btnEl.textContent = data.is_active ? 'Active' : 'Inactive';
                        btnEl.className = data.is_active 
                            ? 'badge badge-outline-success cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1'
                            : 'badge badge-outline-danger cursor-pointer hover:opacity-80 transition text-[11px] px-2.5 py-1';
                    }
                } catch (e) {
                    console.error('Toggle failed', e);
                }
            }
        };
    }
</script>
@endpush

