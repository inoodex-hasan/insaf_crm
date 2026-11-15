<?php

namespace Database\Seeders;

use App\Models\CostingTemplate;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CostingTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $malaysia = Country::where('name', 'like', '%Malaysia%')->first();
        $malta = Country::where('name', 'like', '%Malta%')->first();
        $korea = Country::where('name', 'like', '%Korea%')->first();
        $russia = Country::where('name', 'like', '%Russia%')->first();

        $templates = [
            // 1. Malaysia Preset
            [
                'country_id' => $malaysia?->id,
                'name' => 'Malaysia Higher Education & EMGS Package',
                'currency' => 'MYR',
                'default_exchange_rate' => 30.00,
                'notes' => 'Standard costing breakdown for Malaysian colleges and universities with EMGS visa approval fee.',
                'items' => [
                    [
                        'category' => 'tuition',
                        'title' => 'Course Tuition Fee',
                        'payable_to' => 'university',
                        'currency' => 'MYR',
                        'default_foreign_amount' => 15000.00,
                        'default_bdt_amount' => 450000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'category' => 'scholarship',
                        'title' => 'Scholarship / Tuition Waiver',
                        'payable_to' => 'university',
                        'currency' => 'MYR',
                        'default_foreign_amount' => 0.00,
                        'default_bdt_amount' => 0.00,
                        'is_deduction' => true,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 2,
                    ],
                    [
                        'category' => 'application_fee',
                        'title' => 'University Application / Admission Fee',
                        'payable_to' => 'university',
                        'currency' => 'MYR',
                        'default_foreign_amount' => 500.00,
                        'default_bdt_amount' => 15000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 3,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'EMGS Visa Approval & Processing Fee',
                        'payable_to' => 'government_embassy',
                        'currency' => 'MYR',
                        'default_foreign_amount' => 2200.00,
                        'default_bdt_amount' => 66000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 4,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Medical Screening, Insurance & Attestation',
                        'payable_to' => 'third_party',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 10000.00,
                        'default_bdt_amount' => 10000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'category' => 'agency_fee',
                        'title' => 'Insaf Agency Service Package (Post-Approval)',
                        'payable_to' => 'agency',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 40000.00,
                        'default_bdt_amount' => 40000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 6,
                    ],
                ],
            ],

            // 2. Malta Preset
            [
                'country_id' => $malta?->id,
                'name' => 'Malta Study Permit & State CVU Package',
                'currency' => 'EUR',
                'default_exchange_rate' => 135.00,
                'notes' => 'Standard costing breakdown for Malta student visa including CVU verification and VFS fees.',
                'items' => [
                    [
                        'category' => 'tuition',
                        'title' => 'Course Tuition Fee',
                        'payable_to' => 'university',
                        'currency' => 'EUR',
                        'default_foreign_amount' => 6500.00,
                        'default_bdt_amount' => 877500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'category' => 'scholarship',
                        'title' => 'Scholarship / Tuition Waiver',
                        'payable_to' => 'university',
                        'currency' => 'EUR',
                        'default_foreign_amount' => 0.00,
                        'default_bdt_amount' => 0.00,
                        'is_deduction' => true,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 2,
                    ],
                    [
                        'category' => 'application_fee',
                        'title' => 'University Registration / Application Fee',
                        'payable_to' => 'university',
                        'currency' => 'EUR',
                        'default_foreign_amount' => 100.00,
                        'default_bdt_amount' => 13500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 3,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'State / CVU Verification Fee (Identità Malta)',
                        'payable_to' => 'government_embassy',
                        'currency' => 'EUR',
                        'default_foreign_amount' => 300.00,
                        'default_bdt_amount' => 40500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 4,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'VFS Global Appointment & Visa Submission Fee',
                        'payable_to' => 'government_embassy',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 12000.00,
                        'default_bdt_amount' => 12000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Document Apostille, MOFA & Legalization',
                        'payable_to' => 'third_party',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 8000.00,
                        'default_bdt_amount' => 8000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 6,
                    ],
                    [
                        'category' => 'agency_fee',
                        'title' => 'Insaf Agency Service Package (Post-Visa)',
                        'payable_to' => 'agency',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 50000.00,
                        'default_bdt_amount' => 50000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 7,
                    ],
                ],
            ],

            // 3. South Korea Preset
            [
                'country_id' => $korea?->id,
                'name' => 'South Korea D-2/D-4 Study Permit Package',
                'currency' => 'USD',
                'default_exchange_rate' => 125.00,
                'notes' => 'Standard costing breakdown for South Korean colleges & universities.',
                'items' => [
                    [
                        'category' => 'tuition',
                        'title' => 'Course Tuition Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 4500.00,
                        'default_bdt_amount' => 562500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'category' => 'scholarship',
                        'title' => 'Scholarship / Tuition Waiver (30-50%)',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 0.00,
                        'default_bdt_amount' => 0.00,
                        'is_deduction' => true,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 2,
                    ],
                    [
                        'category' => 'application_fee',
                        'title' => 'University Application / Screening Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 100.00,
                        'default_bdt_amount' => 12500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 3,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Embassy Visa Application Fee',
                        'payable_to' => 'government_embassy',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 7000.00,
                        'default_bdt_amount' => 7000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 4,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Korean Notarization, Solvency & Courier',
                        'payable_to' => 'third_party',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 15000.00,
                        'default_bdt_amount' => 15000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'category' => 'agency_fee',
                        'title' => 'Insaf Agency Service Package (Post-Offer/Visa)',
                        'payable_to' => 'agency',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 120000.00,
                        'default_bdt_amount' => 120000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 6,
                    ],
                ],
            ],

            // 4. Russia Preset
            [
                'country_id' => $russia?->id,
                'name' => 'Russia Study Permit & Ministry Invitation Package',
                'currency' => 'USD',
                'default_exchange_rate' => 125.00,
                'notes' => 'Standard costing breakdown for Russian State Universities and Ministry Invitations.',
                'items' => [
                    [
                        'category' => 'tuition',
                        'title' => 'Course Tuition Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 3000.00,
                        'default_bdt_amount' => 375000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'category' => 'scholarship',
                        'title' => 'Scholarship / Tuition Waiver',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 0.00,
                        'default_bdt_amount' => 0.00,
                        'is_deduction' => true,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 2,
                    ],
                    [
                        'category' => 'application_fee',
                        'title' => 'University Application & Ministry Invitation Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 150.00,
                        'default_bdt_amount' => 18750.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 3,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Russian Embassy Visa Stamping Fee',
                        'payable_to' => 'government_embassy',
                        'currency' => 'USD',
                        'default_foreign_amount' => 80.00,
                        'default_bdt_amount' => 10000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 4,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Russian Translation, Notarization & HIV/Medical',
                        'payable_to' => 'third_party',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 15000.00,
                        'default_bdt_amount' => 15000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'category' => 'agency_fee',
                        'title' => 'Insaf Agency Service Package',
                        'payable_to' => 'agency',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 60000.00,
                        'default_bdt_amount' => 60000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 6,
                    ],
                ],
            ],

            // 5. General / Standard Preset
            [
                'country_id' => null,
                'name' => 'General / Global Destination Study Package',
                'currency' => 'USD',
                'default_exchange_rate' => 125.00,
                'notes' => 'Universal standard costing breakdown template.',
                'items' => [
                    [
                        'category' => 'tuition',
                        'title' => 'Course Tuition Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 5000.00,
                        'default_bdt_amount' => 625000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'category' => 'scholarship',
                        'title' => 'Scholarship / Tuition Waiver',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 0.00,
                        'default_bdt_amount' => 0.00,
                        'is_deduction' => true,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 2,
                    ],
                    [
                        'category' => 'application_fee',
                        'title' => 'University Application / Admission Fee',
                        'payable_to' => 'university',
                        'currency' => 'USD',
                        'default_foreign_amount' => 100.00,
                        'default_bdt_amount' => 12500.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 3,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Embassy / Visa Processing Fee',
                        'payable_to' => 'government_embassy',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 15000.00,
                        'default_bdt_amount' => 15000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 4,
                    ],
                    [
                        'category' => 'additional_cost',
                        'title' => 'Medical Checkup, Solvency & Translation',
                        'payable_to' => 'third_party',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 15000.00,
                        'default_bdt_amount' => 15000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 5,
                    ],
                    [
                        'category' => 'agency_fee',
                        'title' => 'Insaf Agency Service Package',
                        'payable_to' => 'agency',
                        'currency' => 'BDT',
                        'default_foreign_amount' => 50000.00,
                        'default_bdt_amount' => 50000.00,
                        'is_deduction' => false,
                        'is_tuition_placeholder' => false,
                        'sort_order' => 6,
                    ],
                ],
            ],
        ];

        foreach ($templates as $tplData) {
            $items = $tplData['items'];
            unset($tplData['items']);

            $template = CostingTemplate::updateOrCreate(
                ['name' => $tplData['name']],
                $tplData
            );

            // Re-seed items
            $template->items()->delete();
            foreach ($items as $item) {
                $template->items()->create($item);
            }
        }
    }
}
