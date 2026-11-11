@extends('admin.layouts.master')

@section('title', 'Student Details')

@section('content')
<div x-data="agreementGeneratorHandler()">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-xl font-semibold uppercase">Student Details</h2>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Generate Agreement Button --}}
            <button type="button" @click="openAgreementModal()" class="btn btn-outline-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Generate Agreement
            </button>

            <a href="{{ url()->previous() }}" class="btn btn-secondary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back
            </a>
            @can('*consultant')
                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit Student
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Personal Information</h5>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white-dark mb-1">Full Name</label>
                        <p class="font-semibold">{{ $student->first_name }} {{ $student->last_name }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Father's Name</label>
                        <p class="font-semibold">{{ $student->father_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Mother's Name</label>
                        <p class="font-semibold">{{ $student->mother_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Email</label>
                        <p class="font-semibold">{{ $student->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Password</label>
                        <p class="font-semibold">{{ $student->plain_password ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Passport Number</label>
                        <p class="font-semibold">{{ $student->passport_number ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Passport Validity</label>
                        <p class="font-semibold">
                            {{ $student->passport_validity ? \Carbon\Carbon::parse($student->passport_validity)->format('M d, Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Phone</label>
                        @if ($student->phone)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', (string)$student->phone);
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
                                    <span>{{ $student->phone }}</span>
                                </a>
                            </p>
                        @else
                            <p class="font-semibold">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Sponsor Phone</label>
                        @if ($student->sponsor_phone)
                            @php
                                $cleanSponsorPhone = preg_replace('/[^0-9]/', '', (string)$student->sponsor_phone);
                                if (str_starts_with($cleanSponsorPhone, '01') && strlen($cleanSponsorPhone) === 11) {
                                    $waSponsorNumber = '88' . $cleanSponsorPhone;
                                } elseif (str_starts_with($cleanSponsorPhone, '8801') && strlen($cleanSponsorPhone) === 13) {
                                    $waSponsorNumber = $cleanSponsorPhone;
                                } else {
                                    $waSponsorNumber = $cleanSponsorPhone;
                                }
                            @endphp
                            <p>
                                <a href="https://wa.me/{{ $waSponsorNumber }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 font-semibold text-primary hover:text-success hover:underline"
                                    title="Chat on WhatsApp">
                                    <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>{{ $student->sponsor_phone }}</span>
                                </a>
                            </p>
                        @else
                            <p class="font-semibold">-</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Date of Birth</label>
                        <p class="font-semibold">
                            {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('M d, Y') : '-' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-white-dark mb-1">Address</label>
                        <p class="font-semibold whitespace-pre-line">{{ $student->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Preferred Academic
                        Destination</h5>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white-dark mb-1">Country</label>
                        <p class="font-semibold">{{ $student->country->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">University</label>
                        <p class="font-semibold">{{ $student->university->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Course</label>
                        <p class="font-semibold">{{ $student->course->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Intake</label>
                        <p class="font-semibold">{{ $student->intake->intake_name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Documents</h5>
                </div>
                @if ($student->documents && count($student->documents) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($student->documents as $doc)
                    <div class="flex items-center gap-3 p-3 border rounded-lg bg-gray-50 dark:bg-black/20">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-medium truncate" title="{{ $doc['name'] }}">
                                {{ $doc['name'] }}
                            </p>
                            <div class="flex gap-2 mt-1">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" target="_blank"
                                    class="text-xs text-primary hover:underline font-semibold">View PDF</a>
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" download
                                    class="text-xs text-green-600 hover:underline font-semibold">Download</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-white-dark italic text-sm">No documents uploaded.</p>
                @endif
            </div> --}}

            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">University Applications
                    </h5>
                    <!-- <a href="{{ route('admin.applications.create', ['student_id' => $student->id]) }}"
                        class="btn btn-sm btn-primary">New Application</a> -->
                </div>
                @if ($student->applications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table-hover w-full table-auto text-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <!-- <th>University</th>
                                    <th>Course</th> -->
                                    <th>Status</th>
                                    <th>Offer Letter</th>
                                    <th>VFS</th>
                                    <th>File Submit</th>
                                    <th>Visa</th>
                                    <!-- <th>Priority</th> -->
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->applications as $app)
                                    <tr>
                                        <td class="font-bold">{{ $app->application_id }}</td>
                                        <!-- <td>{{ $app->university->name ?? 'N/A' }}</td>
                                        <td>{{ $app->course->name ?? 'N/A' }}</td> -->
                                        <td>
                                            <span
                                                class="badge badge-outline-primary capitalize text-xs">{{ str_replace('_', ' ', $app->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($app->offer_letter_received)
                                                <span class="badge badge-outline-success text-xs">Yes</span>
                                                @if ($app->offer_letter_received_date)
                                                    <div class="text-xs text-white-dark mt-1">
                                                        {{ $app->offer_letter_received_date->format('M d, Y') }}</div>
                                                @endif
                                            @else
                                                <span class="badge badge-outline-danger text-xs">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($app->vfs_appointment)
                                                <span class="badge badge-outline-success text-xs">Yes</span>
                                                @if ($app->vfs_appointment_date)
                                                    <div class="text-xs text-white-dark mt-1">
                                                        {{ $app->vfs_appointment_date->format('M d, Y') }}</div>
                                                @endif
                                            @else
                                                <span class="badge badge-outline-danger text-xs">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($app->file_submission)
                                                <span class="badge badge-outline-success text-xs">Yes</span>
                                                @if ($app->file_submission_date)
                                                    <div class="text-xs text-white-dark mt-1">
                                                        {{ $app->file_submission_date->format('M d, Y') }}</div>
                                                @endif
                                            @else
                                                <span class="badge badge-outline-danger text-xs">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-outline-{{ $app->visa_status === 'approved' ? 'success' : ($app->visa_status === 'rejected' ? 'danger' : 'warning') }} text-xs">
                                                {{ ucfirst(str_replace('_', ' ', $app->visa_status)) }}
                                            </span>
                                        </td>
                                        <!-- <td class="text-center">
                                            <span
                                                class="badge badge-outline-{{ $app->application_priority === 'vip' ? 'danger' : ($app->application_priority === 'priority' ? 'warning' : 'info') }} text-xs">
                                                {{ ucfirst($app->application_priority) }}
                                            </span>
                                        </td> -->
                                        <!-- <td>
                                            <a href="{{ route('admin.applications.edit', $app->id) }}"
                                                class="text-primary hover:underline">Edit</a>
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white-dark italic text-sm">No applications found.</p>
                @endif
            </div>
        </div>

        <!-- System & Assignment Info -->
        <div class="space-y-6">
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Preferred Academic
                        Destination</h5>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white-dark mb-1">Country</label>
                        <p class="font-semibold">{{ $student->country->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">University</label>
                        <p class="font-semibold">{{ $student->university->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Course</label>
                        <p class="font-semibold">{{ $student->course->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Intake</label>
                        <p class="font-semibold">{{ $student->intake->intake_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Subject</label>
                        <p class="font-semibold">{{ $student->subject ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Package Amount</label>
                        <p class="font-semibold text-success">
                            {{ $student->package_amount !== null ? 'BDT ' . number_format($student->package_amount, 2) : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Documents</h5>
                </div>

                @if ($student->documents && count($student->documents) > 0)
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-white-dark uppercase">General Documents</p>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($student->documents as $doc)
                                @php
                                    $path = $doc['path'] ?? null;
                                    $name = $doc['name'] ?? 'Document';
                                    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $previewUrl = $path ? route('preview-file', ['path' => $path]) : null;
                                    $downloadUrl = $path ? route('download-file', ['path' => $path]) : null;
                                @endphp
                                <div class="rounded-lg border border-white-light bg-gray-50 p-3 dark:border-[#1b2e4b] dark:bg-black/20">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold break-words" title="{{ $name }}">{{ $name }}</p>
                                            <p class="text-xs text-white-dark uppercase">{{ $extension ?: 'file' }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if ($previewUrl)
                                                <a href="{{ $previewUrl }}" target="_blank"
                                                    class="text-xs text-primary hover:underline font-semibold whitespace-nowrap">
                                                    Show
                                                </a>
                                            @endif
                                            @if ($downloadUrl)
                                                <a href="{{ $downloadUrl }}"
                                                    class="text-xs text-success hover:underline font-semibold whitespace-nowrap">
                                                    Download
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($student->translation_documents && count($student->translation_documents) > 0)
                    <div class="space-y-3 mt-4">
                        <p class="text-xs font-bold text-white-dark uppercase">Translation Documents</p>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($student->translation_documents as $doc)
                                @php
                                    $path = $doc['path'] ?? null;
                                    $name = $doc['name'] ?? 'Document';
                                    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $previewUrl = $path ? route('preview-file', ['path' => $path]) : null;
                                    $downloadUrl = $path ? route('download-file', ['path' => $path]) : null;
                                @endphp
                                <div class="rounded-lg border border-white-light bg-gray-50 p-3 dark:border-[#1b2e4b] dark:bg-black/20">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold break-words" title="{{ $name }}">{{ $name }}</p>
                                            <p class="text-xs text-white-dark uppercase">{{ $extension ?: 'file' }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if ($previewUrl)
                                                <a href="{{ $previewUrl }}" target="_blank"
                                                    class="text-xs text-secondary hover:underline font-semibold whitespace-nowrap">
                                                    Show
                                                </a>
                                            @endif
                                            @if ($downloadUrl)
                                                <a href="{{ $downloadUrl }}"
                                                    class="text-xs text-success hover:underline font-semibold whitespace-nowrap">
                                                    Download
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if((!$student->documents || count($student->documents) == 0) && (!$student->translation_documents || count($student->translation_documents) == 0))
                    <p class="text-white-dark italic text-sm">No documents uploaded.</p>
                @endif
            </div>
            @can('*consultant')
                <div class="panel border-danger">
                    <h5 class="font-semibold text-lg mb-5 text-danger">Danger Zone</h5>
                    <p class="text-xs text-white-dark mb-4">Deleting this student will remove all associated data permanently.
                    </p>
                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                        onsubmit="return confirm('Are you absolutely sure? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-full">Delete Student</button>
                    </form>
                </div>
            @endcan
            {{-- <div class="panel h-full">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light text-primary uppercase">Status & Assignment</h5>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-white-dark mb-1">Current Stage</label>
                        <div>
                            <span class="badge badge-outline-primary capitalize text-base">{{ $student->current_stage
                                }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Application Status</label>
                        <div>
                            <span class="badge badge-outline-primary capitalize text-base">{{ str_replace('_', ' ',
                                $student->current_status) }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Milestone Remark</label>
                        <div>
                            @if($student->remarks)
                                <span class="badge bg-primary/10 text-primary border border-primary/30 font-semibold text-sm py-1 px-2.5">
                                    {{ $student->remarks }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">No remark assigned</span>
                            @endif
                        </div>
                    </div>
                    @if($student->notes)
                    <div>
                        <label class="text-white-dark mb-1">Counselor Notes</label>
                        <p class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-[#1b2e4b] p-2.5 rounded border border-gray-200 dark:border-gray-700 whitespace-pre-line">
                            {{ $student->notes }}
                        </p>
                    </div>
                    @endif
                    <hr class="border-white-light dark:border-[#1b2e4b] my-4">
                    <div>
                        <label class="text-white-dark mb-1">Marketing Assignee</label>
                        <p class="font-semibold">{{ $student->marketingAssignee->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Consultant Assignee</label>
                        <p class="font-semibold">{{ $student->consultantAssignee->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <label class="text-white-dark mb-1">Application Assignee</label>
                        <p class="font-semibold">{{ $student->applicationAssignee->name ?? 'Unassigned' }}</p>
                    </div>
                    <hr class="border-white-light dark:border-[#1b2e4b] my-4">
                    <div class="text-xs space-y-1">
                        <p><span class="text-white-dark">Collected By:</span> {{ $student->creator->name ?? 'System' }}
                        </p>
                        <p><span class="text-white-dark">Created At:</span> {{ $student->created_at->format('M d, Y ') }}
                        </p>
                        <p><span class="text-white-dark">Last Updated:</span>
                            {{ $student->updated_at->format('M d, Y ') }}</p>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    {{-- Interactive Agreement Generator Modal (Auto-fill + Edit) --}}
    <template x-teleport="#modal-portal">
        <div x-cloak
             x-show="agreementModalOpen"
             class="modal-overlay-full"
             style="display: none;">
            <div class="flex min-h-screen items-center justify-center px-4 py-8">
                <div @click.away="agreementModalOpen = false"
                     x-show="agreementModalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-2xl rounded-xl border border-[#e0e6ed] bg-white shadow-xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b] overflow-hidden">

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                        <div>
                            <h5 class="text-base font-bold text-dark dark:text-white-light">Generate Student Agreement</h5>
                            <p class="text-xs text-white-dark mt-0.5">Fields are auto-filled from student profile and completely editable.</p>
                        </div>
                        <button type="button" @click="agreementModalOpen = false" class="text-white-dark hover:text-dark dark:hover:text-white transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Form --}}
                    <form action="{{ route('admin.learning-resources.generate-agreement') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="action_type" x-model="form.action_type">

                        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                            {{-- Select Template --}}
                            <div class="form-group">
                                <label class="font-bold text-dark dark:text-white-light">Select Agreement Template <span class="text-danger">*</span></label>
                                <select name="template_id" class="form-select" x-model="form.template_id" @change="onTemplateChange()" required>
                                    @foreach($agreementTemplates as $tmpl)
                                        <option value="{{ $tmpl->id }}" 
                                                data-country="{{ strtolower($tmpl->country->name ?? '') }}"
                                                data-layout="{{ $tmpl->layout_type }}">
                                            {{ $tmpl->title }} ({{ $tmpl->layout_type === 'stamp' ? 'Stamp Paper' : 'Agency Pad' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Agreement Date</label>
                                    <input type="text" name="date" class="form-input" x-model="form.date" placeholder="e.g. 04th November 2026">
                                </div>
                                <div class="form-group">
                                    <label>Applicant Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="applicant_name" class="form-input" x-model="form.applicant_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Father's Name</label>
                                    <input type="text" name="father_name" class="form-input" x-model="form.father_name" placeholder="Father's Name">
                                </div>
                                <div class="form-group">
                                    <label>Mother's Name</label>
                                    <input type="text" name="mother_name" class="form-input" x-model="form.mother_name" placeholder="Mother's Name">
                                </div>
                                <div class="form-group">
                                    <label>Passport Number</label>
                                    <input type="text" name="passport_no" class="form-input" x-model="form.passport_no" placeholder="Passport No">
                                </div>
                                <div class="form-group">
                                    <label>Phone / Contact</label>
                                    <input type="text" name="phone" class="form-input" x-model="form.phone" placeholder="Phone Number">
                                </div>
                                <div class="form-group md:col-span-2">
                                    <label>Full Address</label>
                                    <input type="text" name="address" class="form-input" x-model="form.address" placeholder="Village/Area, Thana, District">
                                </div>
                            </div>

                            <hr class="border-[#e0e6ed] dark:border-[#1b2e4b]">

                            {{-- Financial & Academic Particulars --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label>Agency Service Charge</label>
                                    <input type="text" name="service_charge" class="form-input" x-model="form.service_charge" placeholder="e.g. BDT 50,000">
                                </div>
                                <div class="form-group">
                                    <label>Service Charge (In Words)</label>
                                    <input type="text" name="service_charge_words" class="form-input" x-model="form.service_charge_words" placeholder="e.g. Fifty Thousand Taka only">
                                </div>
                                <div class="form-group">
                                    <label>Target University (Optional)</label>
                                    <input type="text" name="university_name" class="form-input" x-model="form.university_name" placeholder="University Name">
                                </div>
                                <div class="form-group">
                                    <label>Target Course (Optional)</label>
                                    <input type="text" name="course_name" class="form-input" x-model="form.course_name" placeholder="Course Name">
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#e0e6ed] bg-[#fbfbfb] px-6 py-4 dark:border-[#1b2e4b] dark:bg-[#121e32]">
                            <button type="button" @click="agreementModalOpen = false" class="btn btn-outline-danger">Cancel</button>

                            <div class="flex items-center gap-2">

                                {{-- Download DOCX --}}
                                <button type="submit" @click="form.action_type = 'docx'" class="btn btn-primary gap-1.5 text-xs" style="background-color: #2563eb;">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Word (.docx)
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>
</div>

<script>
function agreementGeneratorHandler() {
    return {
        agreementModalOpen: false,
        form: {
            template_id: '{{ $agreementTemplates->first()?->id ?? 1 }}',
            action_type: 'preview',
            date: '{{ now()->format('d F Y') }}',
            applicant_name: @js(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))),
            father_name: @js($student->father_name ?? ''),
            mother_name: @js($student->mother_name ?? ''),
            passport_no: @js($student->passport_number ?? ''),
            phone: @js($student->phone ?? ''),
            address: @js($student->address ?? ''),
            service_charge: 'BDT 50,000',
            service_charge_words: 'Fifty Thousand Taka only',
            university_name: @js($student->university->name ?? ''),
            course_name: @js($student->course->name ?? ''),
            intake_name: @js($student->intake->intake_name ?? ''),
        },
        openAgreementModal() {
            this.onTemplateChange();
            this.agreementModalOpen = true;
        },
        onTemplateChange() {
            const select = document.querySelector('select[name="template_id"]');
            if (select) {
                const opt = select.options[select.selectedIndex];
                const country = (opt ? opt.getAttribute('data-country') : '') || '';
                if (country.includes('korea')) {
                    this.form.service_charge = 'BDT 120,000';
                    this.form.service_charge_words = 'One Lac Twenty Thousand Taka only';
                } else if (country.includes('malta')) {
                    this.form.service_charge = 'BDT 50,000';
                    this.form.service_charge_words = 'Fifty Thousand Taka only';
                }
            }
        }
    };
}
</script>
@endsection
