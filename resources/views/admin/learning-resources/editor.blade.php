@extends('admin.layouts.master')

@section('title', 'Word Document Studio (1:1 MS Word Layout) - Insaf CRM')

@push('styles')
<!-- Word Studio Layout Styles -->
<style>
    .word-studio-wrapper {
        background-color: #525659;
        background-image: radial-gradient(#3c4043 1px, transparent 1px);
        background-size: 20px 20px;
        min-height: calc(100vh - 170px);
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        padding: 24px 16px 60px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    .dark .word-studio-wrapper {
        background-color: #1e2227;
        background-image: radial-gradient(#111418 1px, transparent 1px);
    }

    /* Target container for 1:1 MS Word docx-preview */
    #docxRenderContainer {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Style the actual rendered docx-preview pages to look identical to MS Word */
    .docx-wrapper {
        background: transparent !important;
        padding: 0 !important;
    }
    .docx-wrapper > section.docx {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;
        margin-bottom: 30px !important;
        border-radius: 2px !important;
        background: #ffffff !important;
        outline: none !important;
    }
    .docx-wrapper > section.docx[contenteditable="true"]:focus {
        box-shadow: 0 0 0 2px #2563eb, 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
    }

    /* Ribbon buttons */
    .ribbon-btn {
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .ribbon-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .dark .ribbon-btn {
        background: #1e293b;
        color: #e2e8f0;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div x-data="wordStudioApp()" x-init="initApp()">

    <!-- Top Action / Control Bar -->
    <div class="mb-3 rounded-xl border border-[#e0e6ed] bg-white p-3.5 shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
        <div class="flex flex-wrap items-center justify-between gap-3">
            
            <!-- Left: Back & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.learning-resources.index') }}" class="btn btn-outline-secondary btn-sm p-2 flex items-center justify-center rounded-lg" title="Back to Templates">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h5 class="text-base font-bold text-dark dark:text-white-light flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Word Document Studio (1:1 Native MS Word Format)
                    </h5>
                    <p class="text-[11px] text-white-dark mt-0.5">Renders the actual official <code>.docx</code> master file with exact watermarks, table alignments, fonts &amp; margins.</p>
                </div>
            </div>

            <!-- Right: Actions (Autofill, Print, Download DOCX) -->
            <div class="flex flex-wrap items-center gap-2">
                
                <!-- Quick Student Auto-Fill Dropdown -->
                <div class="relative" x-data="{ studentDropdownOpen: false }">
                    <button type="button" 
                            @click="studentDropdownOpen = !studentDropdownOpen" 
                            class="btn btn-outline-primary btn-sm flex items-center gap-1.5 text-xs font-semibold">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span x-text="selectedStudent ? selectedStudent.name : 'Autofill From Student'">Autofill From Student</span>
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Student Search Popper -->
                    <div x-show="studentDropdownOpen" 
                         @click.away="studentDropdownOpen = false" 
                         x-transition
                         style="display: none; position: absolute; right: 0; z-index: 99999; width: 340px;"
                         class="mt-2 rounded-xl border border-[#e0e6ed] bg-white p-3 shadow-2xl dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
                        <div class="mb-2">
                            <input type="text" 
                                   x-model="studentSearchQuery" 
                                   @input.debounce.300ms="searchStudents()"
                                   placeholder="Search by name, passport, phone..." 
                                   class="form-input text-xs py-1.5 w-full">
                        </div>

                        <div class="max-h-60 overflow-y-auto divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
                            <template x-if="isSearchingStudents">
                                <div class="py-3 text-center text-xs text-white-dark">Searching applicants...</div>
                            </template>
                            <template x-if="!isSearchingStudents && studentResults.length === 0">
                                <div class="py-3 text-center text-xs text-white-dark">No applicants found</div>
                            </template>
                            <template x-for="st in studentResults" :key="st.id">
                                <div @click="selectStudent(st); studentDropdownOpen = false" 
                                     class="p-2 hover:bg-slate-50 dark:hover:bg-[#121e32] cursor-pointer rounded transition">
                                    <div class="font-bold text-xs text-dark dark:text-white-light" x-text="st.name"></div>
                                    <div class="text-[11px] text-white-dark flex items-center gap-2 mt-0.5">
                                        <span x-show="st.passport" x-text="'Pass: ' + st.passport"></span>
                                        <span x-show="st.country" class="font-semibold text-primary" x-text="st.country"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Print Button -->
                <button type="button" @click="printDocx()" class="btn btn-outline-secondary btn-sm flex items-center gap-1.5 text-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>

                <!-- Download Populated 1:1 Official DOCX File -->
                <button type="button" 
                        @click="downloadOfficialDocx()" 
                        class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold"
                        style="background-color: #2563eb; color: #ffffff;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Official .docx
                </button>

            </div>

        </div>

        <!-- Template Selector & Document Title Bar -->
        <div class="mt-3 pt-2.5 border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Active Template</label>
                <select class="form-select text-xs py-1.5 w-full" x-model="selectedTemplateId" @change="onTemplateSelectChange()">
                    @foreach($templates as $tmpl)
                        <option value="{{ $tmpl->id }}">
                            {{ $tmpl->title }} ({{ $tmpl->layout_type === 'stamp' ? 'Stamp Paper' : 'Agency Pad' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-[2] min-w-[280px]">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Document Output Title</label>
                <input type="text" x-model="documentTitle" class="form-input text-xs py-1.5 w-full" placeholder="e.g. Malaysia Agreement - John Doe">
            </div>

            <div class="pt-5 flex items-center gap-2">
                <button type="button" 
                        @click="reloadMasterDocx()" 
                        class="btn btn-outline-danger btn-sm text-xs py-1.5"
                        title="Reload the clean master template file">
                    Reload Original .docx
                </button>
            </div>
        </div>
    </div>

    <!-- Main Workspace: Split View with Quick Variable Drawer + Real 1:1 MS Word Document Pages -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">

        <!-- Left Drawer: Quick Variables (3 cols on desktop) -->
        <div class="lg:col-span-3 space-y-3">
            <div class="rounded-xl border border-[#e0e6ed] bg-white p-3.5 shadow-sm dark:border-[#1b2e4b] dark:bg-[#1b2e4b]">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
                    <h6 class="text-xs font-bold uppercase tracking-wider text-dark dark:text-white-light flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Variables &amp; Particulars
                    </h6>
                    <button type="button" @click="applyVariablesToDocx()" class="btn btn-primary btn-sm text-xs px-2 py-0.5 font-bold">
                        Update .docx
                    </button>
                </div>
                <p class="text-[11px] text-white-dark mb-2.5 leading-snug">
                    Edit variables below or click directly on any sentence in the MS Word pages to type edits live.
                </p>

                <div class="space-y-2.5 max-h-[calc(100vh-320px)] overflow-y-auto pr-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Execution Date</label>
                        <input type="text" x-model="variables.DATE" class="form-input text-xs py-1">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Applicant Name</label>
                        <input type="text" x-model="variables.APPLICANT_NAME" @input="updateDocumentTitle()" class="form-input text-xs py-1 font-semibold">
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Father's Name</label>
                            <input type="text" x-model="variables.FATHER_NAME" class="form-input text-xs py-1">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Mother's Name</label>
                            <input type="text" x-model="variables.MOTHER_NAME" class="form-input text-xs py-1">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Passport No</label>
                            <input type="text" x-model="variables.PASSPORT_NO" class="form-input text-xs py-1">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Phone</label>
                            <input type="text" x-model="variables.PHONE" class="form-input text-xs py-1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Address</label>
                        <textarea x-model="variables.ADDRESS" rows="2" class="form-textarea text-xs py-1"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Service Charge</label>
                            <input type="text" x-model="variables.SERVICE_CHARGE" class="form-input text-xs py-1">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Charge Words</label>
                            <input type="text" x-model="variables.SERVICE_CHARGE_WORDS" class="form-input text-xs py-1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Country</label>
                        <input type="text" x-model="variables.COUNTRY_NAME" class="form-input text-xs py-1">
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">University</label>
                            <input type="text" x-model="variables.UNIVERSITY_NAME" class="form-input text-xs py-1">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Course</label>
                            <input type="text" x-model="variables.COURSE_NAME" class="form-input text-xs py-1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Intake</label>
                        <input type="text" x-model="variables.INTAKE_NAME" class="form-input text-xs py-1">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Documents List</label>
                        <textarea x-model="variables.DOCUMENTS_LIST" rows="2" class="form-textarea text-xs py-1"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: 1:1 MS Word Rendering Canvas (9 cols on desktop) -->
        <div class="lg:col-span-9">
            
            <!-- Quick Format Toolbar -->
            <div class="flex items-center justify-between gap-2 p-2 bg-white dark:bg-[#1b2e4b] border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-t-xl">
                <div class="flex items-center gap-1">
                    <button type="button" @click="formatDoc('bold')" class="ribbon-btn font-bold">B</button>
                    <button type="button" @click="formatDoc('italic')" class="ribbon-btn italic">I</button>
                    <button type="button" @click="formatDoc('underline')" class="ribbon-btn underline">U</button>
                    <span class="mx-1 text-slate-300">|</span>
                    <button type="button" @click="formatDoc('justifyLeft')" class="ribbon-btn">Left</button>
                    <button type="button" @click="formatDoc('justifyCenter')" class="ribbon-btn">Center</button>
                    <button type="button" @click="formatDoc('justifyRight')" class="ribbon-btn">Right</button>
                    <button type="button" @click="formatDoc('justifyFull')" class="ribbon-btn">Justify</button>
                </div>
                <div class="text-[11px] text-white-dark flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Exact MS Word OOXML File Rendering</span>
                </div>
            </div>

            <!-- Physical MS Word Desk View -->
            <div class="word-studio-wrapper rounded-b-xl">
                
                <!-- Loading State -->
                <div x-show="isLoadingDocx" class="py-20 text-center text-white space-y-2">
                    <svg class="animate-spin h-8 w-8 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs font-semibold">Rendering 1:1 MS Word Document Layout...</p>
                </div>

                <!-- docx-preview Container (renders genuine Word pages with watermark & table layouts) -->
                <div id="docxRenderContainer" x-show="!isLoadingDocx"></div>

            </div>

        </div>

    </div>

</div>

<!-- JSZip & docx-preview Engine -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://unpkg.com/docx-preview@0.3.3/dist/docx-preview.min.js"></script>

<script>
function wordStudioApp() {
    return {
        templates: @json($templates->map(fn($t) => ['id' => $t->id, 'title' => $t->title, 'layout_type' => $t->layout_type])),
        currentTemplateId: {{ $template->id }},
        selectedTemplateId: '{{ $template->id }}',
        selectedStudent: @json($student ? ['id' => $student->id, 'name' => $student->full_name] : null),
        studentSearchQuery: '',
        studentResults: [],
        isSearchingStudents: false,
        isLoadingDocx: true,
        documentTitle: {!! json_encode(($template->title ?? 'Agreement') . ($student ? ' - ' . $student->full_name : '')) !!},
        currentBlob: null,

        variables: {
            DATE: '{{ $initialValues["DATE"] ?? now()->format("d F Y") }}',
            APPLICANT_NAME: '{{ $initialValues["APPLICANT_NAME"] ?? "" }}',
            FATHER_NAME: '{{ $initialValues["FATHER_NAME"] ?? "" }}',
            MOTHER_NAME: '{{ $initialValues["MOTHER_NAME"] ?? "" }}',
            PASSPORT_NO: '{{ $initialValues["PASSPORT_NO"] ?? "" }}',
            PHONE: '{{ $initialValues["PHONE"] ?? "" }}',
            ADDRESS: '{{ $initialValues["ADDRESS"] ?? "" }}',
            SERVICE_CHARGE: '{{ $initialValues["SERVICE_CHARGE"] ?? "BDT 50,000" }}',
            SERVICE_CHARGE_WORDS: '{{ $initialValues["SERVICE_CHARGE_WORDS"] ?? "Fifty Thousand Taka only" }}',
            COUNTRY_NAME: '{{ $initialValues["COUNTRY_NAME"] ?? "" }}',
            UNIVERSITY_NAME: '{{ $initialValues["UNIVERSITY_NAME"] ?? "" }}',
            COURSE_NAME: '{{ $initialValues["COURSE_NAME"] ?? "" }}',
            INTAKE_NAME: '{{ $initialValues["INTAKE_NAME"] ?? "" }}',
            DOCUMENTS_LIST: '{{ $initialValues["DOCUMENTS_LIST"] ?? "Passport, SSC (Certificate & Transcript), HSC (Certificate & Transcript)" }}',
        },

        initApp() {
            this.loadAndRenderDocx();
        },

        async loadAndRenderDocx() {
            this.isLoadingDocx = true;
            const container = document.getElementById('docxRenderContainer');
            if (container) container.innerHTML = '';

            try {
                // Fetch populated master docx file directly from server
                const studentParam = this.selectedStudent ? `&student_id=${this.selectedStudent.id}` : '';
                const url = `{{ url('dashboard/learning-resources/editor/raw-docx') }}/${this.selectedTemplateId}?${studentParam}`;
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Failed to load docx binary');
                }

                const blob = await response.blob();
                this.currentBlob = blob;

                // Render 1:1 MS Word layout via docx-preview
                await docx.renderAsync(blob, container, null, {
                    className: "docx",
                    inWrapper: true,
                    ignoreWidth: false,
                    ignoreHeight: false,
                    ignoreFonts: false,
                    breakPages: true,
                    ignoreLastRenderedPageBreak: false,
                    experimental: true,
                    trimXmlDeclaration: true,
                    useBase64URL: true,
                });

                // Make all rendered Word pages directly contenteditable so user can edit text
                const pages = container.querySelectorAll('section.docx');
                pages.forEach(p => {
                    p.setAttribute('contenteditable', 'true');
                    p.setAttribute('spellcheck', 'false');
                });

            } catch (err) {
                console.error('Docx render error:', err);
                if (container) {
                    container.innerHTML = `<div class="p-8 text-center text-rose-300 font-semibold">Error rendering MS Word file: ${err.message}</div>`;
                }
            } finally {
                this.isLoadingDocx = false;
            }
        },

        applyVariablesToDocx() {
            this.loadAndRenderDocx();
        },

        reloadMasterDocx() {
            this.loadAndRenderDocx();
        },

        onTemplateSelectChange() {
            this.currentTemplateId = this.selectedTemplateId;
            const tmpl = this.templates.find(t => t.id == this.selectedTemplateId);
            if (tmpl) {
                this.documentTitle = tmpl.title + (this.variables.APPLICANT_NAME ? ' - ' + this.variables.APPLICANT_NAME : '');
            }
            this.loadAndRenderDocx();
        },

        updateDocumentTitle() {
            const tmpl = this.templates.find(t => t.id == this.selectedTemplateId);
            if (tmpl && this.variables.APPLICANT_NAME) {
                this.documentTitle = tmpl.title + ' - ' + this.variables.APPLICANT_NAME;
            }
        },

        async searchStudents() {
            if (!this.studentSearchQuery || this.studentSearchQuery.length < 1) {
                this.studentResults = [];
                return;
            }
            this.isSearchingStudents = true;
            try {
                const res = await fetch(`{{ route('admin.learning-resources.api.students-search') }}?q=${encodeURIComponent(this.studentSearchQuery)}`);
                if (res.ok) {
                    this.studentResults = await res.json();
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.isSearchingStudents = false;
            }
        },

        selectStudent(st) {
            this.selectedStudent = st;
            this.variables.APPLICANT_NAME = st.name || '';
            this.variables.FATHER_NAME = st.father_name || '';
            this.variables.MOTHER_NAME = st.mother_name || '';
            this.variables.PASSPORT_NO = st.passport || '';
            this.variables.PHONE = st.phone || '';
            this.variables.ADDRESS = st.address || '';
            this.variables.COUNTRY_NAME = st.country || this.variables.COUNTRY_NAME;
            this.variables.UNIVERSITY_NAME = st.university || '';
            this.variables.COURSE_NAME = st.course || '';
            this.variables.INTAKE_NAME = st.intake || '';

            this.updateDocumentTitle();
            this.loadAndRenderDocx();
        },

        formatDoc(cmd, val = null) {
            document.execCommand(cmd, false, val);
        },

        downloadOfficialDocx() {
            // Trigger server-side generation of the exact populated master DOCX file
            const studentParam = this.selectedStudent ? `&student_id=${this.selectedStudent.id}` : '';
            const downloadUrl = `{{ url('dashboard/learning-resources/editor/raw-docx') }}/${this.selectedTemplateId}?download=1${studentParam}`;
            window.location.href = downloadUrl;
        },

        printDocx() {
            window.print();
        }
    };
}
</script>
@endsection