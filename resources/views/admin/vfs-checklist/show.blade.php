@extends('admin.layouts.master')

@section('title', 'VFS Checklist: ' . $application->application_id)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold uppercase">VFS Document Checklist</h2>
            <p class="text-xs text-white-dark mt-1">
                {{ $application->application_id }} |
                {{ $application->student->first_name }} {{ $application->student->last_name }} |
                {{ $application->university?->name ?? 'No University' }}
                @if($application->university?->country)
                    | <span class="text-info">{{ $application->university->country->name }}</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.vfs-checklist.templates') }}" class="btn btn-outline-primary">Manage List</a>
            <form action="{{ route('admin.vfs-checklist.sync-templates', $application) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-warning">Sync List</button>
            </form>
            <!-- <form action="{{ route('admin.vfs-checklist.reset-templates', $application) }}" method="POST" class="inline" onsubmit="return confirm('This will delete ALL current items and recreate from templates. Continue?');">
                @csrf
                <button type="submit" class="btn btn-danger">Reset</button>
            </form> -->
            <a href="{{ route('admin.vfs-checklist.index') }}" class="btn btn-secondary gap-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- @if (session('success'))
        <div class="mt-4 p-4 border border-success bg-success/5 text-success rounded">{{ session('success') }}</div>
    @endif -->

    <div class="mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="vfsChecklist()">

            <!-- Progress Sidebar -->
            <div class="space-y-6">
                <div class="panel">
                    <h5 class="text-sm font-semibold uppercase mb-4">Completion Status</h5>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold uppercase text-white-dark">Checked:</span>
                            <span class="font-mono text-lg font-black text-success">
                                <span x-text="checkedCount"></span> / {{ $checklistItems->count() }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                            <div class="h-3 rounded-full transition-all"
                                :class="progress == 100 ? 'bg-success' : 'bg-primary'" :style="'width: ' + progress + '%'">
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold uppercase text-white-dark">Progress:</span>
                            <span class="font-mono text-2xl font-black" :class="{ 'text-success': progress == 100 }"
                                x-text="progress + '%'"></span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-success/10 border border-success/30 rounded-lg text-center"
                        x-show="progress == 100" x-cloak>
                        <svg class="w-10 h-10 mx-auto text-success mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs font-bold text-success uppercase">All Documents Collected!</p>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="panel">
                    <h6 class="text-xs font-bold uppercase mb-4 text-white-dark">Bulk Actions</h6>
                    <div class="space-y-3">

                        <!-- Always visible if unchecked items exist -->
                        <button @click="selectUnchecked()" class="btn btn-sm btn-outline-primary w-full"
                            x-show="uncheckedCount > 0" x-cloak>
                            <span x-text="'Select Unchecked (' + uncheckedCount + ')'"></span>
                        </button>

                        <!-- Show when not all are selected -->
                        <button @click="selectAll()" class="btn btn-sm btn-outline-secondary w-full"
                            x-show="selectedItems.length < totalItems" x-cloak>
                            Select All ({{ $checklistItems->count() }})
                        </button>

                        <!-- Show when items are selected -->
                        <button @click="bulkCheck()" class="btn btn-sm btn-success w-full" x-show="selectedItems.length > 0"
                            x-cloak>
                            ✓ Check Selected (<span x-text="selectedItems.length"></span>)
                        </button>

                        <!-- Uncheck selected -->
                        <button @click="bulkUncheck()" class="btn btn-sm btn-warning w-full"
                            x-show="selectedItems.length > 0" x-cloak>
                            ✕ Uncheck Selected (<span x-text="selectedItems.length"></span>)
                        </button>

                        <!-- Clear selection -->
                        <button @click="selectedItems = []" class="btn btn-sm btn-outline-danger w-full"
                            x-show="selectedItems.length > 0" x-cloak>
                            Clear Selection
                        </button>

                    </div>
                </div>
            </div>

            <!-- Checklist Items -->
            <div class="lg:col-span-2">
                <div class="panel">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="text-lg font-semibold uppercase">Document Items</h5>
                        <span class="text-xs text-white-dark" x-show="selectedItems.length > 0" x-cloak>
                            <span x-text="selectedItems.length"></span> selected
                        </span>
                    </div>

                    <div class="space-y-2">
                        @foreach($checklistItems as $index => $item)
                            <div class="p-4 border rounded-lg transition-all hover:shadow-md flex items-center gap-4 group"
                                :class="{
                                            'bg-success/10 border-success/30': items_{{ $item->id }},
                                            'bg-primary/5 border-primary/30': selectedItems.includes({{ $item->id }}) && !items_{{ $item->id }}
                                        }">

                                <!-- Item Number -->
                                <span class="text-xs font-bold text-white-dark w-6 shrink-0">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <!-- Selection Checkbox -->
                                <input type="checkbox" :value="{{ $item->id }}" x-model="selectedItems"
                                    class="w-5 h-5 rounded cursor-pointer shrink-0" />

                                <!-- Item Details -->
                                <div class="flex-1">
                                    <button @click="toggleItem({{ $item->id }})"
                                        class="font-medium text-left w-full transition-all"
                                        :class="items_{{ $item->id }} ? 'text-success line-through' : ''">
                                        {{ $item->checklist_item }}
                                    </button>
                                    @if($item->checkedBy)
                                        <p class="text-[10px] text-white-dark mt-0.5">
                                            Checked by <span class="font-medium">{{ $item->checkedBy->name }}</span>
                                            on {{ $item->checked_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Notes -->
                                <div class="w-48 shrink-0">
                                    <input type="text" x-model="notes_{{ $item->id }}" @blur="updateNotes({{ $item->id }})"
                                        @keyup.enter="updateNotes({{ $item->id }})" placeholder="Add note..."
                                        class="form-input text-xs py-1" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function vfsChecklist() {
            return {
                selectedItems: [],
                totalItems: {{ $checklistItems->count() }},
                checkedCount: {{ $checklistItems->where('is_checked', true)->count() }},

                get progress() {
                    return this.totalItems > 0
                        ? Math.round((this.checkedCount / this.totalItems) * 100)
                        : 0;
                },

                // Dynamically counts unchecked from live Alpine state
                get uncheckedCount() {
                    return this.allItemIds().filter(id => !this['items_' + id]).length;
                },

                @foreach($checklistItems as $item)
                            items_{{ $item->id }}: {{ $item->is_checked ? 'true' : 'false' }},
                    notes_{{ $item->id }}: '{{ addslashes($item->notes ?? '') }}',
                @endforeach

            // Returns all item IDs as an array
            allItemIds() {
                return [
                    @foreach($checklistItems as $item){{ $item->id }}, @endforeach
                        ];
            },

            toggleItem(itemId) {
                fetch(`{{ route('admin.vfs-checklist.toggle-item', ':item') }}`.replace(':item', itemId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const wasChecked = this['items_' + itemId];
                            this['items_' + itemId] = data.is_checked;

                            if (!wasChecked && data.is_checked) {
                                this.checkedCount++;
                            } else if (wasChecked && !data.is_checked) {
                                this.checkedCount--;
                            }

                            // Remove from selection after toggle
                            const index = this.selectedItems.indexOf(itemId);
                            if (index > -1) this.selectedItems.splice(index, 1);
                        }
                    });
            },

            updateNotes(itemId) {
                fetch(`{{ route('admin.vfs-checklist.update-notes', ':item') }}`.replace(':item', itemId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ notes: this['notes_' + itemId] })
                });
            },

            selectAll() {
                this.selectedItems = this.allItemIds();
            },

            // Now uses live Alpine state — not static PHP
            selectUnchecked() {
                this.selectedItems = this.allItemIds().filter(id => !this['items_' + id]);
            },

            bulkCheck() {
                if (this.selectedItems.length === 0) return;
                const toCheck = [...this.selectedItems];

                fetch(`{{ route('admin.vfs-checklist.bulk-check', $application) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ item_ids: toCheck })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            toCheck.forEach(id => {
                                if (!this['items_' + id]) {
                                    this['items_' + id] = true;
                                    this.checkedCount++;
                                }
                            });
                            this.selectedItems = [];
                        }
                    });
            },

            bulkUncheck() {
                if (this.selectedItems.length === 0) return;
                const toUncheck = [...this.selectedItems];

                fetch(`{{ route('admin.vfs-checklist.bulk-uncheck', $application) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ item_ids: toUncheck })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            toUncheck.forEach(id => {
                                if (this['items_' + id]) {
                                    this['items_' + id] = false;
                                    this.checkedCount--;
                                }
                            });
                            this.selectedItems = [];
                        }
                    });
            }
        }
            }
    </script>
@endpush