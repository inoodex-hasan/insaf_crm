<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $template->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            @if($template->layout_type === 'stamp' && $template->header_space_mm > 0)
            margin-top: {{ $template->header_space_mm }}mm;
            @else
            margin-top: 20mm;
            @endif
            margin-bottom: 20mm;
            margin-left: 20mm;
            margin-right: 20mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .pad-header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .pad-header h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pad-header p {
            font-size: 9pt;
            color: #64748b;
            margin: 0;
        }

        .pad-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            font-size: 8pt;
            color: #94a3b8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            font-size: 10pt;
        }

        h2, h3, h4 {
            color: #0f172a;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @if($template->layout_type === 'pad')
    <div class="pad-header">
        <h1>Insaf Immigration Consultancy</h1>
        <p>Official Student Agreement & Clearance Service Hub | Dhaka, Bangladesh</p>
    </div>

    <div class="pad-footer">
        Page 1 of 1 &bull; Insaf Immigration Consultancy &bull; Confidential Official Agreement Format
    </div>
    @endif

    <div class="document-content">
        {!! $template->content !!}
    </div>

</body>
</html>

