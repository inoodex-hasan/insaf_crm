@extends('admin.layouts.master')

@section('title', 'Lead Details - ' . $lead->student_name)

@section('content')
    <div>
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <h2 class="text-xl font-semibold uppercase">Lead Details</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.marketing.leads.index') }}" class="btn btn-outline-primary">Back to List</a>
                @can('*marketing')
                    <a href="{{ route('admin.marketing.leads.edit', $lead->id) }}" class="btn btn-primary">Edit Lead</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="panel">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="font-semibold text-lg">Student Information</h5>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $lead->priority_badge_class }} uppercase text-xs">
                                {{ $lead->priority ?? 'low' }} Priority
                            </span>
                            <span
                                class="badge @if ($lead->status == 'pending') badge-outline-warning @elseif($lead->status == 'interested') badge-outline-success @elseif($lead->status == 'forwarded') badge-outline-info @else badge-outline-danger @endif capitalize">
                                {{ $lead->status }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-white-dark mb-1">Full Name</label>
                            <p class="font-semibold text-gray-light">{{ $lead->student_name }}</p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Phone Number</label>
                            @if ($lead->phone)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', (string)$lead->phone);
                                    if (str_starts_with($cleanPhone, '01') && strlen($cleanPhone) === 11) {
                                        $waNumber = '88' . $cleanPhone;
                                    } elseif (str_starts_with($cleanPhone, '8801') && strlen($cleanPhone) === 13) {
                                        $waNumber = $cleanPhone;
                                    } else {
                                        $waNumber = $cleanPhone;
                                    }
                                @endphp
                                <p>
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 font-semibold text-primary hover:text-success hover:underline"
                                        title="Chat on WhatsApp">
                                        <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        <span>{{ $lead->phone }}</span>
                                    </a>
                                </p>
                            @else
                                <p class="font-semibold text-gray-light">N/A</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Email Address</label>
                            <p class="font-semibold text-gray-light">{{ $lead->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Current Education</label>
                            <p class="font-semibold text-gray-light">{{ $lead->current_education ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Preferences & Source -->
                <div class="panel">
                    <h5 class="font-semibold text-lg mb-5">Preferences & Source</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-white-dark mb-1">Preferred Country</label>
                            <p class="font-semibold text-gray-light">{{ $lead->country->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Preferred Course</label>
                            <p class="font-semibold text-gray-light">{{ $lead->course->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Contact Source</label>
                            <p class="font-semibold text-gray-light">
                                <span class="badge badge-outline-primary">{{ $lead->source }}</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Priority</label>
                            <p class="font-semibold text-gray-light">
                                <span class="badge {{ $lead->priority_badge_class }} uppercase text-xs">
                                    {{ $lead->priority ?? 'low' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-white-dark mb-1">Follow-up Date</label>
                            @php($followUpTimeline = $lead->follow_up_timeline)
                            @if($followUpTimeline->isNotEmpty())
                                @php($currentFollowUp = $followUpTimeline->last())
                                <p class="font-semibold {{ $currentFollowUp['date']->isPast() ? 'text-danger' : 'text-gray-light' }}">
                                    {{ $currentFollowUp['date']->format('M d, Y') }}
                                </p>
                            @else
                                <p class="font-semibold text-gray-light">N/A</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="flex items-center justify-between mb-5">
                        <h5 class="font-semibold text-lg">Follow-up History</h5>
                        <span class="text-xs text-white-dark">{{ $followUpTimeline->count() }} record(s)</span>
                    </div>

                    @if($followUpTimeline->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($followUpTimeline->reverse()->values() as $index => $followUp)
                                <div class="rounded-lg border border-white-light p-4 dark:border-[#1b2e4b]">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold {{ $followUp['date']->isPast() ? 'text-danger' : 'text-gray-light' }}">
                                                {{ $followUp['date']->format('M d, Y') }}
                                            </p>
                                            <p class="text-xs text-white-dark">
                                                {{ $index === 0 ? 'Latest follow-up' : 'Previous follow-up' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 rounded-md bg-black/10 p-3">
                                        <p class="whitespace-pre-wrap text-sm text-gray-light">
                                            {{ filled($followUp['notes']) ? $followUp['notes'] : 'No note saved for this follow-up.' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg bg-black/10 p-4">
                            <p class="text-gray-light">No follow-up history available.</p>
                        </div>
                    @endif
                </div>

                <!-- Internal Notes -->
                <div class="panel">
                    <h5 class="font-semibold text-lg mb-5">Internal Notes</h5>
                    <div class="bg-black/10 p-4 rounded-lg min-h-[100px]">
                        <p class="whitespace-pre-wrap text-gray-light">{{ $lead->notes ?? 'No notes available.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="space-y-6">
                <div class="panel">
                    <h5 class="font-semibold text-lg mb-5">System Tracking</h5>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white-dark">Collected By:</span>
                            <span class="font-semibold">{{ $lead->creator->name ?? 'System' }}</span>
                        </div>
                        {{-- <div class="flex justify-between">
                            <span class="text-white-dark">Assigned Consultant:</span>
                            <span class="font-semibold">{{ $lead->consultant->name ?? 'Unassigned' }}</span>
                        </div> --}}
                        <hr class="border-white-light dark:border-[#1b2e4b]">
                        <div class="flex justify-between">
                            <span class="text-white-dark">Created At:</span>
                            <span>{{ $lead->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white-dark">Last Updated:</span>
                            <span>{{ $lead->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                @can('*marketing')
                    <div class="panel border-danger">
                        <h5 class="font-semibold text-lg mb-5 text-danger">Danger Zone</h5>
                        <p class="text-xs text-white-dark mb-4">Deleting this lead will remove all associated data permanently.
                        </p>
                        <form action="{{ route('admin.marketing.leads.destroy', $lead->id) }}" method="POST"
                            onsubmit="return confirm('Are you absolutely sure? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-full">Delete Lead</button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@endsection
