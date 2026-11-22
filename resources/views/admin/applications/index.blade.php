@extends('admin.layouts.master')

@section('title', 'Applications')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <h2 class="text-xl font-semibold uppercase">Applications</h2>
    @if (auth()->check() && auth()->user()->hasRole('consultant'))
        @can(['*consultant'])
            <div class="flex w-full flex-wrap items-center justify-end gap-4 sm:w-auto">
                <a href="{{ route('admin.applications.create') }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Application
                </a>
            </div>
        @endcan
    @endif
</div>

<div class="panel mt-6">
    <div class="mb-5">
        <form action="{{ route('admin.applications.index') }}" method="GET" class="flex flex-col gap-3 w-full">

            {{-- Row 1: Search + Status + Offer Letter + Visa --}}
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; width: 100%;">
                <div class="relative" style="position: relative; flex: 2; min-width: 180px;">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search ID, student name..." class="form-input text-xs w-full" style="height: 38px; padding-right: 36px;" />
                    <button type="submit"
                        style="position: absolute; right: 8px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background: none; border: none; padding: 0; cursor: pointer;"
                        class="text-gray-400 hover:text-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </div>

                <select name="status" class="form-select text-xs" style="flex: 1; min-width: 120px; height: 38px;">
                    <option value="">Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    <option value="visa_processing" {{ request('status') == 'visa_processing' ? 'selected' : '' }}>Visa Processing</option>
                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                </select>

                <select name="offer_letter" class="form-select text-xs" style="flex: 1; min-width: 120px; height: 38px;">
                    <option value="">Offer Letter</option>
                    <option value="1" {{ request('offer_letter') == '1' ? 'selected' : '' }}>Received</option>
                    <option value="0" {{ request('offer_letter') == '0' ? 'selected' : '' }}>Not Received</option>
                </select>

                <select name="visa_status" class="form-select text-xs" style="flex: 1; min-width: 120px; height: 38px;">
                    <option value="">Visa</option>
                    <option value="not_applied" {{ request('visa_status') == 'not_applied' ? 'selected' : '' }}>Not Applied</option>
                    <option value="pending" {{ request('visa_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('visa_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('visa_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            {{-- Row 2: EMGS Score + Security Deposit + CVU Fee + Created/Collected By + Filter/Reset buttons --}}
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; width: 100%;">
                <select name="emgs_score" class="form-select text-xs" style="flex: 1; min-width: 110px; height: 38px;">
                    <option value="">EMGS Score</option>
                    @foreach([5, 15, 32, 35, 70] as $score)
                        <option value="{{ $score }}" {{ request('emgs_score') == $score ? 'selected' : '' }}>{{ $score }}%</option>
                    @endforeach
                </select>

                <select name="security_deposit" class="form-select text-xs" style="flex: 1; min-width: 140px; height: 38px;">
                    <option value="">Security Deposit</option>
                    <option value="1" {{ request('security_deposit') == '1' ? 'selected' : '' }}>Paid</option>
                    <option value="0" {{ request('security_deposit') == '0' ? 'selected' : '' }}>Pending</option>
                </select>

                <select name="cvu_fee" class="form-select text-xs" style="flex: 1; min-width: 110px; height: 38px;">
                    <option value="">CVU Fee</option>
                    <option value="1" {{ request('cvu_fee') == '1' ? 'selected' : '' }}>Paid</option>
                    <option value="0" {{ request('cvu_fee') == '0' ? 'selected' : '' }}>Pending</option>
                </select>

                <select name="created_by" class="form-select text-xs" style="flex: 1.5; min-width: 160px; height: 38px;">
                    <option value="">Created / Collected By</option>
                    @foreach($creators ?? [] as $creator)
                        <option value="{{ $creator->id }}" {{ (request('created_by') == $creator->id || request('collected_by') == $creator->id) ? 'selected' : '' }}>
                            {{ $creator->name }}
                        </option>
                    @endforeach
                </select>

                <div style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
                    <button type="submit" class="btn btn-primary text-xs font-semibold px-4" style="height: 38px; white-space: nowrap;">Filter</button>
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-danger text-xs font-semibold px-4" style="height: 38px; white-space: nowrap; display: inline-flex; align-items: center;">Reset</a>
                </div>
            </div>

        </form>
    </div>

    <div class="datatable">
        <div class="table-responsive overflow-x-auto min-h-[280px]" style="min-height: 280px;">
            <table class="table-hover w-full table-auto">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student</th>
                        <th>University & Course</th>
                        <!-- <th>VFS Appointment Date</th> -->
                        <th>Status</th>
                        <!-- <th>Offer Letter</th>
                        <th>VFS</th>
                        <th>File Submit</th>
                        <th>Visa Status</th>  -->
                        <!-- <th>Priority</th> -->
                        <th>Created By</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td class="font-bold text-primary">{{ $app->application_id }}</td>
                        <td>
                            @if ($app->student)
                                <div class="font-semibold">
                                    <a href="{{ route('admin.students.show', $app->student->id) }}" class="text-primary hover:underline">
                                        {{ $app->student->first_name }} {{ $app->student->last_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-white-dark">{{ $app->student->phone }}</div>
                            @else
                                <span class="text-danger italic">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="font-semibold">{{ $app->university->name ?? 'N/A' }}</div>
                            <div class="text-xs text-white-dark">{{ $app->course ? Illuminate\Support\Str::limit($app->course->name, 30) : 'N/A' }}</div>
                        </td>
                        <!-- <td>{{ $app->intake->intake_name ?? 'N/A' }}</td> -->
                        <td>
                            <span
                                class="badge badge-outline-{{ $app->status === 'pending' ? 'warning' : ($app->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-xs">
                                {{ $app->creator->name ?? 'System' }}
                                <div class="text-white-dark">{{ $app->created_at->format('M d, Y') }}</div>
                            </div>
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
                                        <a href="{{ route('admin.applications.show', $app) }}" @click="toggle">
                                            <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            View
                                        </a>
                                    </li>
                                    @canany(['*application|*accountant'])
                                        <li>
                                            <a href="{{ route('admin.applications.edit', $app->id) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.applications.destroy', $app->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this application?');" class="m-0">
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
                                    @endcanany
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center">No applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection
