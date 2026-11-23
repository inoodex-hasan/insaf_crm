@extends('admin.layouts.master')

@section('title', 'Journal Entries')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Journal Ledger</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.journal-entries.report', array_merge(request()->all(), ['output' => 'preview'])) }}"
                target="_blank" class="btn btn-outline-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Preview
            </a>
            <a href="{{ route('admin.journal-entries.report', array_merge(request()->all(), ['output' => 'download'])) }}"
                class="btn btn-outline-success gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download
            </a>
            <a href="{{ route('admin.journal-entries.create') }}" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Create Voucher
            </a>
        </div>
    </div>

    <div class="panel mt-6">
        <!-- Toolbar: KPI summary & Quick Filter -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; width: 100%; border-bottom: 1px solid #e0e6ed; padding-bottom: 16px; margin-bottom: 16px;" class="dark:border-[#1b2e4b]">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg text-xs font-semibold">
                    Total: {{ $totalCount ?? $entries->total() }} Vouchers
                </span>
            </div>

            <form method="GET" action="{{ route('admin.journal-entries.index') }}" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
                <select name="period_id" class="form-select text-xs" style="height: 36px; width: 140px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Periods</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select text-xs" style="height: 36px; width: 120px; border-radius: 6px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                </select>

                <div style="position: relative; width: 200px;">
                    <input type="text" name="reference_number" value="{{ request('reference_number') }}"
                        placeholder="Ref # or Student..." class="form-input text-xs w-full" style="height: 36px; padding-right: 36px; border-radius: 6px;" />
                    <button type="submit"
                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer;"
                        class="text-gray-400 hover:text-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>

                @if(request('reference_number') || request('student_name') || request('period_id') || request('status') || request('start_date') || request('end_date'))
                    <a href="{{ route('admin.journal-entries.index') }}" class="btn btn-outline-danger btn-sm text-xs" style="height: 36px; display: inline-flex; align-items: center;">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive" style="min-height: 280px;">
            <table class="table-hover w-full table-auto">
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Reference</th>
                        <th>Student</th>
                        <th>Period</th>
                        <th>Voucher Amount</th>
                        <th>Posted By</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td class="whitespace-nowrap text-xs font-semibold">{{ $entry->date->format('M d, Y') }}</td>
                            <td class="font-bold underline text-primary">
                                <a
                                    href="{{ route('admin.journal-entries.show', $entry) }}">{{ $entry->reference_number }}</a>
                            </td>
                            <td class="font-xs">
                                @if ($entry->application)
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-black dark:text-white">{{ $entry->application->student->first_name }}
                                            {{ $entry->application->student->last_name }}</span>
                                        <span
                                            class="text-[10px] text-gray-500 uppercase">{{ $entry->application->application_id }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">General Entry</span>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge badge-outline-secondary text-[10px] uppercase">{{ $entry->period->name }}</span>
                            </td>
                            <td class="font-bold">{{ number_format($entry->total_amount, 2) }}</td>
                            <td class="text-xs">{{ $entry->creator->name }}</td>
                            <td class="text-center">
                                <div class="dropdown flex items-center justify-center" x-data="dropdown" @click.outside="open = false">
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-dark/70 hover:bg-gray-100 hover:text-primary dark:text-white-light/70 dark:hover:bg-dark/40 dark:hover:text-primary" @click="toggle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <ul x-cloak x-show="open" x-transition x-transition.duration.300ms :class="openUp ? 'bottom-full mb-1' : 'top-full mt-1'" class="ltr:right-0 rtl:left-0 !z-50 whitespace-nowrap shadow-lg">
                                        <li>
                                            <a href="{{ route('admin.journal-entries.show', $entry) }}" @click="toggle">
                                                <svg class="h-4 w-4 ltr:mr-2 rtl:ml-2 text-info" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.journal-entries.destroy', $entry) }}" method="POST"
                                                class="m-0" onsubmit="return confirm('Delete this voucher?')">
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
                            <td colspan="7" class="text-center text-gray-400 py-16">
                                <div class="flex flex-col items-center">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1" opacity="0.2">
                                        <path
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2">No journal vouchers recorded yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $entries->links() }}
        </div>
    </div>
@endsection
