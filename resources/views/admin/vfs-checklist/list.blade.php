@extends('admin.layouts.master')

@section('title', 'VFS Checklist List')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">VFS Checklist List</h2>
        <div class="flex gap-2">
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

    {{-- Add New Template --}}
    <div class="panel mt-6">
        <h5 class="text-lg font-semibold dark:text-white-light mb-4">Add New List Item</h5>
        <form action="{{ route('admin.vfs-checklist.store-template') }}" method="POST" class="flex flex-col md:flex-row gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" name="item_name" class="form-input" placeholder="Enter checklist item name..." required>
            </div>
            <div class="md:w-48">
                <select name="country_id" class="form-select min-w-[150px]">
                    <option value="">All Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Item</button>
        </form>
    </div>

    {{-- Seed Defaults --}}
    @if($templates->isEmpty())
        <div class="panel mt-6 bg-warning/10 border border-warning/30">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-md font-semibold text-warning">No List Found</h5>
                    <p class="text-sm text-white-dark mt-1">Seed the default checklist items to get started.</p>
                </div>
                <form action="{{ route('admin.vfs-checklist.seed-templates') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">Seed Default Items</button>
                </form>
            </div>
        </div>
    @else
        <div class="panel mt-6">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-lg font-semibold dark:text-white-light">List Items ({{ $templates->count() }})</h5>
                <form action="{{ route('admin.vfs-checklist.seed-templates') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">Restore Missing Defaults</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-hover w-full">
                    <thead>
                        <tr>
                            <th class="w-16">#</th>
                            <th>Item Name</th>
                            <th class="w-32">Country</th>
                            <th class="w-24 text-center">Status</th>
                            <th class="w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-templates">
                        @foreach($templates as $index => $template)
                            <tr data-id="{{ $template->id }}">
                                <td class="cursor-move">
                                    <span class="drag-handle text-white-dark">☰</span>
                                    <span class="sort-number">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.vfs-checklist.update-template', $template) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="item_name" value="{{ $template->item_name }}" class="form-input text-sm" style="min-width:0;">
                                        <input type="hidden" name="country_id" value="{{ $template->country_id }}">
                                        <button type="submit" class="btn btn-sm btn-primary whitespace-nowrap">Save</button>
                                    </form>
                                </td>
                                <td class="text-sm">
                                    <form action="{{ route('admin.vfs-checklist.update-template', $template) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="item_name" value="{{ $template->item_name }}">
                                        <select name="country_id" class="form-select text-sm" onchange="this.form.submit()">
                                            <option value="" {{ is_null($template->country_id) ? 'selected' : '' }}>All</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ $template->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.vfs-checklist.update-template', $template) }}" method="POST" class="inline" id="status-form-{{ $template->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="item_name" value="{{ $template->item_name }}">
                                        <input type="hidden" name="is_active" value="0">
                                        <label class="inline-flex items-center cursor-pointer select-none">
                                            <input type="checkbox" name="is_active" value="1" class="form-checkbox w-5 h-5 text-success rounded border-gray-300 focus:ring-success"
                                                {{ $template->is_active ? 'checked' : '' }}
                                                onchange="document.getElementById('status-form-{{ $template->id }}').submit();">
                                            <span class="ml-2 text-sm {{ $template->is_active ? 'text-success font-semibold' : 'text-gray-500' }}">
                                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </label>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.vfs-checklist.delete-template', $template) }}" method="POST" onsubmit="return confirm('Delete this template item?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('sortable-templates');
            if (tbody) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        const order = Array.from(tbody.children).map(row => row.dataset.id);

                        fetch('{{ route('admin.vfs-checklist.reorder-templates') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update sort numbers
                                document.querySelectorAll('.sort-number').forEach((el, i) => {
                                    el.textContent = i + 1;
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
