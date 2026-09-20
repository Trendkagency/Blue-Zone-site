<?php

namespace Database\Seeders;

use App\Models\Mr\ContactClassification;
use Illuminate\Database\Seeder;

class ContactClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = [
            [
                'code' => 'A+',
                'label' => 'VIP / High Influence (A+)',
                'points' => 4,
                'required_visits' => 4,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'A',
                'label' => 'Key Prescriber (A)',
                'points' => 3,
                'required_visits' => 3,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'B',
                'label' => 'Standard Doctor (B)',
                'points' => 2,
                'required_visits' => 2,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'C',
                'label' => 'Occasional Prescriber (C)',
                'points' => 1,
                'required_visits' => 1,
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($classifications as $item) {
            ContactClassification::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
