@extends('admin.layouts.master')

@section('title', 'Marketing Videos')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Marketing Videos</h2>
    </div>

    <!-- Add New Video -->
    <div class="panel mt-6">
        <h3 class="text-lg font-bold mb-4">Add New Video</h3>
        <form action="{{ route('admin.marketing.videos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <input type="text" name="video_name" placeholder="Video name" class="form-input" required />
            </div>
            <div>
                <select name="status" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="not_edited">Not Edited</option>
                    <option value="editing">Editing</option>
                    <option value="ready">Ready</option>
                    <option value="uploaded">Uploaded</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Add Video</button>
            </div>
        </form>
    </div>

    <!-- Videos Table -->
    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Search -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 20px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $videos->total() }} Videos
                </span>
                <span class="badge bg-success/10 text-success border border-success/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Ready/Uploaded: {{ $readyCount ?? 0 }}
                </span>
            </div>

            <form action="{{ route('admin.marketing.videos.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <select name="status" class="form-select text-xs" style="height: 36px; width: 140px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="not_edited" {{ request('status') == 'not_edited' ? 'selected' : '' }}>Not Edited</option>
                    <option value="editing" {{ request('status') == 'editing' ? 'selected' : '' }}>Editing</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="uploaded" {{ request('status') == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                </select>

                <div style="position: relative; width: 220px;">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search video..."
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

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.marketing.videos.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>
        <div class="datatable">
            <div class="table-responsive overflow-x-auto" style="min-height: 280px;">
                <table class="table-hover w-full table-auto">
                    <thead>
                        <tr>
                            <th>Video Name</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                            <tr>
                                <td>{{ $video->video_name }}</td>
                                <td>
                                    @php
                                        $vClass = [
                                            'pending' => 'badge-outline-dark',
                                            'not_edited' => 'badge-outline-danger',
                                            'editing' => 'badge-outline-warning',
                                            'ready' => 'badge-outline-success',
                                            'uploaded' => 'badge-outline-primary',
                                        ][$video->status] ?? 'badge-outline-dark';
                                    @endphp
                                    <span class="badge {{ $vClass }} capitalize text-[10px]">{{ str_replace('_', ' ', $video->status) }}</span>
                                </td>
                                <td>{{ $video->created_at->format('M d, Y') }}</td>
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
                                                    class="flex w-full items-center px-4 py-2 text-left hover:!bg-primary/10 hover:!text-primary"
                                                    @click="toggle(); document.getElementById('edit-modal-{{ $video->id }}').classList.remove('hidden')">
                                                    <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.marketing.videos.destroy', $video) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this video?');" class="m-0">
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

                            <!-- Edit Modal -->
                            <div id="edit-modal-{{ $video->id }}" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4">
                                <div class="panel w-full max-w-md">
                                    <h3 class="text-lg font-bold mb-4">Edit Video</h3>
                                    <form action="{{ route('admin.marketing.videos.update', $video) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="text-sm font-bold">Video Name</label>
                                                <input type="text" name="video_name" value="{{ $video->video_name }}" class="form-input" required />
                                            </div>
                                            <div>
                                                <label class="text-sm font-bold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="pending" {{ $video->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="not_edited" {{ $video->status == 'not_edited' ? 'selected' : '' }}>Not Edited</option>
                                                    <option value="editing" {{ $video->status == 'editing' ? 'selected' : '' }}>Editing</option>
                                                    <option value="ready" {{ $video->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                                    <option value="uploaded" {{ $video->status == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                                                </select>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('edit-modal-{{ $video->id }}').classList.add('hidden')">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-white-dark">No videos found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $videos->links() }}
            </div>
        </div>
    </div>
@endsection
