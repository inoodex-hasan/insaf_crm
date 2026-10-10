<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Details - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background-color: #333;
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .detail-label {
            font-weight: bold;
            width: 40%;
        }
        .detail-value {
            width: 60%;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Details</h1>
        <p>Student ID: #{{ $student->id }}</p>
    </div>

    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="detail-row">
            <div class="detail-label">Full Name:</div>
            <div class="detail-value">{{ $student->first_name }} {{ $student->last_name }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Father's Name:</div>
            <div class="detail-value">{{ $student->father_name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Mother's Name:</div>
            <div class="detail-value">{{ $student->mother_name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email:</div>
            <div class="detail-value">{{ $student->email ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone:</div>
            <div class="detail-value">{{ $student->phone }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Sponsor Phone:</div>
            <div class="detail-value">{{ $student->sponsor_phone ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Passport Number:</div>
            <div class="detail-value">{{ $student->passport_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Passport Validity:</div>
            <div class="detail-value">{{ $student->passport_validity ? date('M d, Y', strtotime($student->passport_validity)) : 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Date of Birth:</div>
            <div class="detail-value">{{ $student->dob ? date('M d, Y', strtotime($student->dob)) : 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Address:</div>
            <div class="detail-value">{{ $student->address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Academic Information</div>
        <div class="detail-row">
            <div class="detail-label">Country:</div>
            <div class="detail-value">{{ $student->country->name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">University:</div>
            <div class="detail-value">{{ $student->university->name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Course:</div>
            <div class="detail-value">{{ $student->course->name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Intake:</div>
            <div class="detail-value">{{ $student->intake->intake_name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">SSC Result:</div>
            <div class="detail-value">{{ $student->ssc_result ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">HSC Result:</div>
            <div class="detail-value">{{ $student->hsc_result ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">IELTS Score:</div>
            <div class="detail-value">{{ $student->ielts_score ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Subject:</div>
            <div class="detail-value">{{ $student->subject ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Package Amount:</div>
            <div class="detail-value">{{ $student->package_amount !== null ? 'BDT ' . number_format($student->package_amount, 2) : 'N/A' }}</div>
        </div>
    </div>

    @if($student->documents && count($student->documents) > 0)
    <div class="section">
        <div class="section-title">Uploaded Documents</div>
        @foreach($student->documents as $doc)
        <div class="detail-row">
            <div class="detail-label">Document:</div>
            <div class="detail-value">{{ $doc['name'] }}</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($student->translation_documents && count($student->translation_documents) > 0)
    <div class="section">
        <div class="section-title">Translation Documents</div>
        @foreach($student->translation_documents as $doc)
        <div class="detail-row">
            <div class="detail-label">Document:</div>
            <div class="detail-value">{{ $doc['name'] }}</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($student->applications->count() > 0)
    <div class="section">
        <div class="section-title">University Applications</div>
        <table>
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>University</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Offer Letter</th>
                    <th>VFS</th>
                    <th>File Submit</th>
                    <th>Visa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->applications as $app)
                <tr>
                    <td>{{ $app->application_id }}</td>
                    <td>{{ $app->university->name ?? 'N/A' }}</td>
                    <td>{{ $app->course->name ?? 'N/A' }}</td>
                    <td>{{ str_replace('_', ' ', $app->status) }}</td>
                    <td>{{ $app->offer_letter_received ? 'Yes' : 'No' }}</td>
                    <td>{{ $app->vfs_appointment ? 'Yes' : 'No' }}</td>
                    <td>{{ $app->file_submission ? 'Yes' : 'No' }}</td>
                    <td>{{ $app->visa_status ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This document was generated automatically from the INSAF Backend System</p>
        <p>Student ID: {{ $student->id }} | Generated: {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
