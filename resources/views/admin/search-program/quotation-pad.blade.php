<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Costing Breakdown & Financial Quotation — Insaf Educational Consultancy</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        body {
            background: #f1f5f9;
            padding: 20px;
        }
        .quotation-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
            position: relative;
        }
        .header-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .brand-logo-area img {
            max-height: 52px;
            width: auto;
        }
        .brand-text h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0369a1;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .brand-text p {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .header-meta {
            text-align: right;
            font-size: 11px;
            color: #475569;
            line-height: 1.5;
        }
        .header-meta .quotation-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 4px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .info-grid {
            display: flex;
            gap: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }
        .info-col {
            flex: 1;
        }
        .info-col h3 {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }
        .info-row {
            display: flex;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .info-label {
            width: 105px;
            color: #64748b;
            font-weight: 600;
        }
        .info-val {
            flex: 1;
            color: #0f172a;
            font-weight: 700;
        }
        .section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .rate-badge {
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            color: #475569;
        }
        table.cost-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 20px;
        }
        table.cost-table th {
            background: #0369a1;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #0284c7;
        }
        table.cost-table th.text-right {
            text-align: right;
        }
        table.cost-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
        }
        table.cost-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .row-scholarship td {
            background: #f0fdf4 !important;
            color: #15803d;
            font-weight: 700;
        }
        .row-net-tuition td {
            background: #e0f2fe !important;
            color: #0369a1;
            font-weight: 800;
            border-top: 1.5px solid #0284c7;
            border-bottom: 1.5px solid #0284c7;
        }
        .badge-cat {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .cat-tuition { background: #dbeafe; color: #1e40af; }
        .cat-scholarship { background: #dcfce7; color: #15803d; }
        .cat-app { background: #fef3c7; color: #92400e; }
        .cat-additional { background: #ede9fe; color: #5b21b6; }
        .cat-agency { background: #ffedd5; color: #9a3412; }
        
        .kpi-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 22px;
        }
        .kpi-card {
            text-align: center;
        }
        .kpi-card .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }
        .kpi-card .kpi-val {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-card.kpi-grand {
            background: #0369a1;
            color: #ffffff;
            border-radius: 4px;
            padding: 6px 4px;
        }
        .kpi-card.kpi-grand .kpi-label {
            color: #e0f2fe;
        }
        .kpi-card.kpi-grand .kpi-val {
            color: #ffffff;
            font-size: 15px;
        }

        .terms-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-size: 10px;
            color: #78350f;
            line-height: 1.5;
        }
        .terms-box h4 {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .terms-box ul {
            padding-left: 14px;
        }

        .signatures-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 15px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-line {
            border-top: 1.5px dashed #64748b;
            margin-bottom: 6px;
        }
        .sig-block p {
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }

        .actions-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-print {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-close {
            background: #e2e8f0;
            color: #334155;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .quotation-sheet {
                box-shadow: none;
                padding: 10px;
                max-width: 100%;
            }
            .actions-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <button onclick="window.print()" class="btn-print">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Quotation
        </button>
        <button onclick="window.close()" class="btn-close">Close</button>
    </div>

    <div class="quotation-sheet">

        <!-- Header Branding -->
        <div class="header-brand">
            <div class="brand-logo-area">
                <img src="{{ asset('assets/images/logo-insaf.png') }}" onerror="this.src='{{ asset('assets/images/insaf_logo.png') }}'" alt="Insaf Logo">
                <div class="brand-text">
                    <h1>INSAF INTERNATIONAL</h1>
                    <p>Educational Consultancy & Visa Processing Hub</p>
                </div>
            </div>
            <div class="header-meta">
                <div class="quotation-badge">Official Costing Quotation</div>
                <p><strong>Ref:</strong> {{ $data['reference_no'] ?? ('Q-' . date('Ymd') . '-' . rand(100, 999)) }}</p>
                <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
            </div>
        </div>

        <!-- Student & Course Info Grid -->
        <div class="info-grid">
            <div class="info-col">
                <h3>Student Profile</h3>
                @if($student)
                    <div class="info-row"><span class="info-label">Student Name:</span><span class="info-val">{{ $student->full_name }}</span></div>
                    <div class="info-row"><span class="info-label">Passport No:</span><span class="info-val">{{ $student->passport_number ?? 'N/A' }}</span></div>
                    <div class="info-row"><span class="info-label">Contact Phone:</span><span class="info-val">{{ $student->phone ?? 'N/A' }}</span></div>
                    <div class="info-row"><span class="info-label">Counselor:</span><span class="info-val">{{ auth()->user()?->name ?? 'Head Counselor' }}</span></div>
                @else
                    <div class="info-row"><span class="info-label">Client Name:</span><span class="info-val">{{ $data['client_name'] ?? 'Prospective Student' }}</span></div>
                    <div class="info-row"><span class="info-label">Status:</span><span class="info-val">Initial Counseling Inquiry</span></div>
                    <div class="info-row"><span class="info-label">Issued By:</span><span class="info-val">{{ auth()->user()?->name ?? 'Insaf Counselor' }}</span></div>
                @endif
            </div>

            <div class="info-col">
                <h3>Academic & Destination Program</h3>
                <div class="info-row"><span class="info-label">Selected Course:</span><span class="info-val">{{ $data['course_name'] ?? 'Academic Degree Program' }}</span></div>
                <div class="info-row"><span class="info-label">Institution:</span><span class="info-val">{{ $data['university_name'] ?? 'Partner University' }}</span></div>
                <div class="info-row"><span class="info-label">Destination:</span><span class="info-val">{{ $data['country_name'] ?? 'Overseas' }}</span></div>
                <div class="info-row"><span class="info-label">Program Level:</span><span class="info-val">{{ $data['program_level'] ?? 'Undergraduate / Post-Graduate' }}</span></div>
            </div>
        </div>

        <!-- Four Core Cost Components Table -->
        <div class="section-title">
            <span>Itemized Financial Costing Breakdown</span>
            <span class="rate-badge">Rate Applied: 1 {{ $data['currency'] ?? 'USD' }} = {{ number_format((float)($data['exchange_rate'] ?? 1), 2) }} BDT</span>
        </div>

        <table class="cost-table">
            <thead>
                <tr>
                    <th style="width: 38%;">Description & Milestone</th>
                    <th style="width: 15%;">Payable To</th>
                    <th style="width: 14%;">Category</th>
                    <th class="text-right" style="width: 16%;">Foreign Amount</th>
                    <th class="text-right" style="width: 17%;">BDT Equivalent</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = $data['items'] ?? [];
                    $curr = $data['currency'] ?? 'USD';
                @endphp

                @forelse($items as $item)
                    @php
                        $isDeduction = !empty($item['is_deduction']);
                        $isScholarship = ($item['category'] ?? '') === 'scholarship';
                        $catClass = match($item['category'] ?? '') {
                            'tuition' => 'cat-tuition',
                            'scholarship' => 'cat-scholarship',
                            'application_fee' => 'cat-app',
                            'additional_cost' => 'cat-additional',
                            'agency_fee' => 'cat-agency',
                            default => 'cat-additional',
                        };
                    @endphp
                    <tr class="{{ $isScholarship ? 'row-scholarship' : '' }}">
                        <td>
                            <strong>{{ $item['title'] }}</strong>
                            @if($isScholarship)
                                <span style="font-size: 9px; display: block; color: #16a34a;">(Direct scholarship discount from institution)</span>
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $item['payable_to'] ?? 'Third Party')) }}</td>
                        <td>
                            <span class="badge-cat {{ $catClass }}">{{ str_replace('_', ' ', $item['category'] ?? 'cost') }}</span>
                        </td>
                        <td class="text-right">
                            @if(($item['currency'] ?? 'BDT') !== 'BDT')
                                {{ $isDeduction ? '-' : '' }}{{ $item['currency'] }} {{ number_format((float)($item['foreign_amount'] ?? 0), 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right" style="font-weight: 700;">
                            {{ $isDeduction ? '-' : '' }}BDT {{ number_format((float)($item['bdt_amount'] ?? 0), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">No detailed line items entered.</td>
                    </tr>
                @endforelse

                <!-- Net Tuition Highlight Row -->
                <tr class="row-net-tuition">
                    <td colspan="4">
                        <strong>NET TUITION PAYABLE (After Scholarship / Tuition Waiver)</strong>
                    </td>
                    <td class="text-right">
                        <strong>BDT {{ number_format((float)($data['net_tuition_fee'] ?? 0), 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Core Financial KPI Metrics -->
        <div class="kpi-summary-grid">
            <div class="kpi-card">
                <div class="kpi-label">1. Net Tuition Fee</div>
                <div class="kpi-val text-primary">BDT {{ number_format((float)($data['net_tuition_fee'] ?? 0), 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">2. App & State/Addl Fees</div>
                <div class="kpi-val">BDT {{ number_format((float)(($data['application_fee'] ?? 0) + ($data['total_additional_costs'] ?? 0)), 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">3. Agency Service Fee</div>
                <div class="kpi-val">BDT {{ number_format((float)($data['agency_fee'] ?? 0), 2) }}</div>
            </div>
            <div class="kpi-card kpi-grand">
                <div class="kpi-label">Est. Grand Total Budget</div>
                <div class="kpi-val">BDT {{ number_format((float)($data['grand_total_bdt'] ?? 0), 2) }}</div>
            </div>
        </div>

        <!-- Terms & Advisory Notes -->
        <div class="terms-box">
            <h4>Important Notice & Financial Terms:</h4>
            <ul>
                <li>Tuition fee and scholarship figures are based on the latest university admission schedule and are subject to academic transcript evaluation.</li>
                <li>Foreign currency conversions (EUR, USD, MYR) are calculated at the current indicative bank rate and may vary at the time of final international wire transfer.</li>
                <li>EMGS approval fees, State/CVU fees, and Embassy/VFS stamping charges are statutory government fees paid directly to authorized portals.</li>
                <li>Insaf International service charges are payable in accordance with the official Service Agreement signed at the time of file opening.</li>
            </ul>
        </div>

        <!-- Signature Authorization Blocks -->
        <div class="signatures-area">
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>Prepared By / Counselor</p>
                <span style="font-size: 9px; color: #64748b;">Insaf Educational Consultancy</span>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <p>Student / Guardian Signature</p>
                <span style="font-size: 9px; color: #64748b;">I acknowledge the estimated breakdown</span>
            </div>
        </div>

    </div>

</body>
</html>
