<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->title }} - Preview</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .paper-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto 40px auto;
            padding: 20mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            box-sizing: border-box;
            position: relative;
        }

        @if($template->layout_type === 'stamp' && $template->header_space_mm > 0)
        .stamp-top-clearance {
            height: {{ $template->header_space_mm }}mm;
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @endif

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .paper-sheet {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            @if($template->layout_type === 'stamp' && $template->header_space_mm > 0)
            .stamp-top-clearance {
                height: {{ $template->header_space_mm }}mm;
                border: none;
                background: none;
                color: transparent;
            }
            @endif
        }
    </style>
</head>
<body>

    <!-- Floating Top Bar -->
    <div class="no-print sticky top-0 z-50 flex items-center justify-between border-b border-[#e0e6ed] bg-white px-6 py-3 shadow-md">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.learning-resources.index', ['tab' => in_array($template->type, ['clearance_withdrawal', 'clearance_full']) ? 'clearances' : 'agreements']) }}" 
               class="flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Learning Resources
            </a>
            <span class="text-slate-300">|</span>
            <span class="text-sm font-bold text-slate-800">{{ $template->title }}</span>
            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $template->layout_type === 'stamp' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                {{ $template->layout_type === 'stamp' ? 'Stamp Paper Format (' . $template->header_space_mm . 'mm Top Margin)' : 'Agency Official Pad Format' }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.learning-resources.templates.download-pdf', $template) }}" 
               class="btn btn-danger btn-sm flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs" style="background-color: #e11d48; color: white;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>

            <a href="{{ route('admin.learning-resources.templates.download-docx', $template) }}" 
               class="btn btn-primary btn-sm flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs" style="background-color: #2563eb; color: white;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download DOCX (Word)
            </a>

            <button type="button" onclick="window.print()" 
                    class="btn btn-dark btn-sm flex items-center gap-1.5 px-3 py-1.5 rounded-md font-semibold text-xs" style="background-color: #0f172a; color: white;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Document
            </button>
        </div>
    </div>

    <!-- Paper Sheet Container -->
    <div class="paper-sheet">
        @if($template->layout_type === 'stamp' && $template->header_space_mm > 0)
        <!-- Stamp clearance indicator (visible on screen preview, blank space on physical print) -->
        <div class="stamp-top-clearance">
            [ Non-Judicial Stamp Header Clearance Area ({{ $template->header_space_mm }}mm) ]
        </div>
        @elseif($template->layout_type === 'pad')
        <!-- Agency Official Letterhead -->
        <div style="text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 24px;">
            <h1 style="font-size: 20pt; font-weight: bold; color: #1e293b; margin: 0 0 4px 0; text-transform: uppercase;">INSAF IMMIGRATION CONSULTANCY</h1>
            <p style="font-size: 10pt; color: #64748b; margin: 0;">Official Student Agreement & Clearance Service Hub | Dhaka, Bangladesh</p>
        </div>
        @endif

        <!-- Document Body Content -->
        <div class="document-body">
            {!! $template->content !!}
        </div>
    </div>

</body>
</html>

