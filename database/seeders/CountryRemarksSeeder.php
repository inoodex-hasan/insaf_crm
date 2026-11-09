<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryRemark;
use Illuminate\Database\Seeder;

class CountryRemarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $malta = Country::whereRaw('LOWER(name) = ?', ['malta'])->first();
        $malaysia = Country::whereRaw('LOWER(name) = ?', ['malaysia'])->first();
        $russia = Country::whereRaw('LOWER(name) = ?', ['russia'])->first();

        $remarksData = [
            'malta' => [
                'country_id' => $malta?->id,
                'items' => [
                    ['name' => 'Offer Letter Pending', 'color' => 'primary'],
                    ['name' => 'Application Pending', 'color' => 'primary'],
                    ['name' => 'Slot Pending', 'color' => 'warning'],
                    ['name' => 'Submission Pending', 'color' => 'warning'],
                    ['name' => 'Visa Approval', 'color' => 'success'],
                    ['name' => 'Visa Refused', 'color' => 'danger'],
                    ['name' => 'Flight Done', 'color' => 'info'],
                ],
            ],
            'malaysia' => [
                'country_id' => $malaysia?->id,
                'items' => [
                    ['name' => 'Pending Apply', 'color' => 'primary'],
                    ['name' => 'Pending OL', 'color' => 'primary'],
                    ['name' => 'Pending EMGS', 'color' => 'warning'],
                    ['name' => 'EMGS - 5%', 'color' => 'info'],
                    ['name' => 'EMGS - 15%', 'color' => 'info'],
                    ['name' => 'EMGS - 32%', 'color' => 'info'],
                    ['name' => 'EMGS - 35%', 'color' => 'info'],
                    ['name' => 'EMGS - 70%', 'color' => 'info'],
                    ['name' => 'Visa Apply', 'color' => 'primary'],
                    ['name' => 'Interview', 'color' => 'warning'],
                    ['name' => 'Visa Approved', 'color' => 'success'],
                    ['name' => 'Visa Reject', 'color' => 'danger'],
                    ['name' => 'Flight Done', 'color' => 'secondary'],
                ],
            ],
            'russia' => [
                'country_id' => $russia?->id,
                'items' => [
                    ['name' => 'Translation Pending', 'color' => 'warning'],
                    ['name' => 'Application Done', 'color' => 'primary'],
                    ['name' => 'Waiting for OL', 'color' => 'primary'],
                    ['name' => 'Waiting for Invitation', 'color' => 'warning'],
                    ['name' => 'Waiting for Submission', 'color' => 'warning'],
                    ['name' => 'Visa Approved', 'color' => 'success'],
                    ['name' => 'Visa Reject', 'color' => 'danger'],
                    ['name' => 'Flight Done', 'color' => 'info'],
                ],
            ],
            'others' => [
                'country_id' => null, // Default for rest of the world
                'items' => [
                    ['name' => 'Pending Application', 'color' => 'primary'],
                    ['name' => 'OL Received', 'color' => 'info'],
                    ['name' => 'OL Pending', 'color' => 'warning'],
                    ['name' => 'Embassy Submission', 'color' => 'warning'],
                    ['name' => 'Visa Approved', 'color' => 'success'],
                    ['name' => 'Visa Reject', 'color' => 'danger'],
                    ['name' => 'Flight Done', 'color' => 'secondary'],
                ],
            ],
        ];

        foreach ($remarksData as $groupKey => $group) {
            $countryId = $group['country_id'];
            $sortOrder = 10;

            foreach ($group['items'] as $item) {
                CountryRemark::updateOrCreate(
                    [
                        'country_id' => $countryId,
                        'name' => $item['name'],
                    ],
                    [
                        'color' => $item['color'] ?? 'primary',
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                    ]
                );
                $sortOrder += 10;
            }
        }
    }
}

