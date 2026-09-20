<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\VisitCycle;
use App\Models\Role;
use App\Models\User;
use App\Services\Mr\CrmAssignmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MrSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Classifications & Specialties & GPS Config
        $this->call([
            ContactClassificationSeeder::class,
            ContactSpecialtySeeder::class,
            MrGpsConfigSeeder::class,
        ]);

        // 2. Create MR Role if not exists
        $mrRole = Role::firstOrCreate(
            ['name' => 'mr'],
            [
                'description' => 'Medical Representative field agent',
                'permissions' => json_encode([
                    'mr.*',
                    'mr.visits',
                    'mr.schedule',
                    'mr.contacts.view',
                ]),
            ]
        );

        $lineManagerRole = Role::firstOrCreate(
            ['name' => 'mr_line_manager'],
            [
                'description' => 'Medical Representative Line Manager',
                'permissions' => json_encode([
                    'mr.*',
                    'mr.reports',
                    'mr.assignments',
                ]),
            ]
        );

        // 3. Create Demo Active Visit Cycle
        $now = now();
        $cycle = VisitCycle::firstOrCreate(
            ['code' => 'CYCLE-' . $now->format('Y-m')],
            [
                'name' => $now->format('F Y') . ' Cycle',
                'start_date' => $now->copy()->startOfMonth(),
                'end_date' => $now->copy()->endOfMonth(),
                'status' => 'active',
                'notes' => 'Active monthly field visit cycle for medical representatives.',
            ]
        );

        // 4. Create Demo MR User if none exists
        $mrUser = User::firstOrCreate(
            ['email' => 'rep@bluezone.com'],
            [
                'name' => 'Dr. Kareem Tarek (MR)',
                'password' => Hash::make('password'),
                'role_id' => $mrRole->id,
                'status' => 'active',
            ]
        );

        // 5. Seed Sample Doctor Contacts
        $cairo = City::first();
        $country = Country::first();
        $specialties = ContactSpecialty::all();
        $classA_plus = ContactClassification::where('code', 'A+')->first();
        $classA = ContactClassification::where('code', 'A')->first();
        $classB = ContactClassification::where('code', 'B')->first();
        $classC = ContactClassification::where('code', 'C')->first();

        $doctors = [
            [
                'code' => 'DOC-101',
                'name' => 'Dr. Ahmed El-Sherif',
                'hospital_clinic_name' => 'Cairo Heart & Longevity Center',
                'specialty_id' => $specialties->where('code', 'CARD')->first()?->id ?? 1,
                'classification_id' => $classA_plus?->id ?? 1,
                'latitude' => 30.044420,
                'longitude' => 31.235712,
                'region' => 'Nasr City',
                'address' => '45 Abbas El-Akkad St, Nasr City, Cairo',
                'phone' => '+201001122334',
                'email' => 'drahmed@cairoheart.com',
            ],
            [
                'code' => 'DOC-102',
                'name' => 'Dr. Sarah Mansour',
                'hospital_clinic_name' => 'Metabolic Longevity Clinic',
                'specialty_id' => $specialties->where('code', 'ENDO')->first()?->id ?? 1,
                'classification_id' => $classA?->id ?? 2,
                'latitude' => 30.056000,
                'longitude' => 31.330000,
                'region' => 'Heliopolis',
                'address' => '12 Baghdad St, Korba, Heliopolis, Cairo',
                'phone' => '+201005544332',
                'email' => 'drsarah@metabolic.com',
            ],
            [
                'code' => 'DOC-103',
                'name' => 'Dr. Mohamed Nabil',
                'hospital_clinic_name' => 'Advanced Neurology Institute',
                'specialty_id' => $specialties->where('code', 'NEUR')->first()?->id ?? 1,
                'classification_id' => $classB?->id ?? 3,
                'latitude' => 30.013100,
                'longitude' => 31.208900,
                'region' => 'Maadi',
                'address' => '9 Road 250, Degla, Maadi, Cairo',
                'phone' => '+201112233445',
                'email' => 'drnabil@neurolongevity.com',
            ],
            [
                'code' => 'DOC-104',
                'name' => 'Dr. Laila Hassan',
                'hospital_clinic_name' => 'Zamalek Wellness & Aesthetics',
                'specialty_id' => $specialties->where('code', 'DERM')->first()?->id ?? 1,
                'classification_id' => $classC?->id ?? 4,
                'latitude' => 30.062600,
                'longitude' => 31.219700,
                'region' => 'Zamalek',
                'address' => '22 26th of July St, Zamalek, Cairo',
                'phone' => '+201223344556',
                'email' => 'drlaila@zamalekwellness.com',
            ],
        ];

        $assignmentService = app(CrmAssignmentService::class);

        foreach ($doctors as $docData) {
            $doc = Contact::updateOrCreate(
                ['code' => $docData['code']],
                array_merge($docData, [
                    'city_id' => $cairo?->id,
                    'country_id' => $country?->id,
                    'is_active' => true,
                ])
            );

            // Assign to demo MR
            $assignmentService->assignContact($cycle->id, $mrUser->id, $doc->id);
        }
    }
}
