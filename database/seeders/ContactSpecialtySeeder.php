<?php

namespace Database\Seeders;

use App\Models\Mr\ContactSpecialty;
use Illuminate\Database\Seeder;

class ContactSpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Cardiology', 'code' => 'CARD', 'description' => 'Heart and vascular medicine'],
            ['name' => 'Neurology', 'code' => 'NEUR', 'description' => 'Brain and nervous system'],
            ['name' => 'Pediatrics', 'code' => 'PED', 'description' => 'Child and infant healthcare'],
            ['name' => 'Orthopedics', 'code' => 'ORTH', 'description' => 'Bones, joints, and muscular system'],
            ['name' => 'Oncology', 'code' => 'ONC', 'description' => 'Cancer therapies and management'],
            ['name' => 'Endocrinology', 'code' => 'ENDO', 'description' => 'Hormones and metabolism'],
            ['name' => 'General Medicine', 'code' => 'GEN', 'description' => 'Family and internal general practice'],
            ['name' => 'Dermatology', 'code' => 'DERM', 'description' => 'Skin, hair, and aesthetic longevity'],
        ];

        foreach ($specialties as $item) {
            ContactSpecialty::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
