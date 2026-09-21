<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['specialty', 'classification', 'city', 'country'])
            ->withCount(['assignments', 'visits']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('hospital_clinic_name', 'like', "%{$s}%")
                  ->orWhere('region', 'like', "%{$s}%");
            });
        }

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->integer('specialty_id'));
        }

        if ($request->filled('classification_id')) {
            $query->where('classification_id', $request->integer('classification_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('export')) {
            return $this->exportContacts($query->get(), $request);
        }

        $contacts = $query->latest()->paginate(15)->withQueryString();
        $specialties = ContactSpecialty::where('is_active', true)->get();
        $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.mr.contacts.index', compact('contacts', 'specialties', 'classifications', 'cities'));
    }

    protected function exportContacts($contacts, Request $request)
    {
        $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);

        $metadata = [
            'Search Query' => $request->search ?: null,
            'Specialty Filter' => $request->filled('specialty_id') ? ContactSpecialty::find($request->specialty_id)?->name : 'All Specialties',
            'Class Filter' => $request->filled('classification_id') ? ('Class ' . ContactClassification::find($request->classification_id)?->code) : 'All Classes',
            'City Filter' => $request->filled('city_id') ? City::find($request->city_id)?->name_en : 'All Cities',
            'Status' => $request->filled('status') ? ucfirst($request->status) : 'All Statuses',
            'Total Contacts' => $contacts->count(),
        ];

        $totalDoctors = $contacts->count();
        $classAPlus = $contacts->filter(fn($c) => strtoupper($c->classification?->code ?? '') === 'A+')->count();
        $classA = $contacts->filter(fn($c) => strtoupper($c->classification?->code ?? '') === 'A')->count();
        $activeCount = $contacts->where('is_active', true)->count();
        $totalVisits = $contacts->sum('visits_count');

        $kpiCards = [
            ['label' => 'Total Contacts', 'val' => (string)$totalDoctors, 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
            ['label' => 'Class A+ Doctors', 'val' => (string)$classAPlus, 'bg' => 'FEF3C7', 'fg' => '92400E', 'border' => 'FDE68A'],
            ['label' => 'Class A Doctors', 'val' => (string)$classA, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
            ['label' => 'Active Contacts', 'val' => (string)$activeCount, 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
            ['label' => 'Recorded Visits', 'val' => (string)$totalVisits, 'bg' => 'EDE9FE', 'fg' => '6D28D9', 'border' => 'C4B5FD'],
        ];

        $columns = [
            ['key' => fn($c) => $c->code ?? 'N/A', 'header' => 'Contact Code', 'width' => 14, 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($c) => $c->name, 'header' => 'Doctor / Contact Name', 'width' => 26, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($c) => $c->specialty?->name ?? 'General', 'header' => 'Specialty', 'width' => 18, 'align' => Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($c) => $c->classification?->code ?? 'C',
                'header' => 'Class',
                'width' => 10,
                'type' => 'badge',
                'align' => Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match (strtoupper((string)$val)) {
                    'A+' => ['bg' => 'FEF3C7', 'fg' => '92400E'],
                    'A' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                    'B' => ['bg' => 'F1F5F9', 'fg' => '334155'],
                    default => ['bg' => 'F8FAFC', 'fg' => '64748B'],
                }
            ],
            ['key' => fn($c) => $c->hospital_clinic_name ?: '—', 'header' => 'Hospital / Clinic', 'width' => 25, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($c) => ($c->region ? $c->region . ', ' : '') . ($c->city?->name_en ?? $c->city?->name_ar ?? 'N/A'), 'header' => 'Region / City', 'width' => 20, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($c) => $c->address ?: '—', 'header' => 'Address', 'width' => 28, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($c) => $c->phone ?: '—', 'header' => 'Phone', 'width' => 16, 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($c) => (int)$c->assignments_count, 'header' => 'Active Assigns', 'width' => 14, 'type' => 'number', 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($c) => (int)$c->visits_count, 'header' => 'Visits Executed', 'width' => 14, 'type' => 'number', 'align' => Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($c) => $c->is_active ? 'Active' : 'Inactive',
                'header' => 'Status',
                'width' => 12,
                'type' => 'badge',
                'align' => Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => $val === 'Active' ? ['bg' => 'DCFCE7', 'fg' => '15803D'] : ['bg' => 'FEE2E2', 'fg' => 'B91C1C']
            ],
        ];

        $summaryConfig = [
            'col' => 'A',
            'mergeTo' => 'H',
            'label' => 'PORTFOLIO TOTALS',
            'align' => Alignment::HORIZONTAL_RIGHT,
        ];

        return $exporter->export(
            'Doctor Portfolio & Clinic Directory',
            $metadata,
            $kpiCards,
            $columns,
            $contacts,
            'doctors-directory-' . date('Y-m-d') . '.xlsx',
            [
                'col' => 'A',
                'mergeTo' => 'H',
                'label' => 'TOTAL PORTFOLIO CONTACTS',
                'align' => Alignment::HORIZONTAL_RIGHT,
            ]
        );
    }

    public function create()
    {
        $specialties = ContactSpecialty::where('is_active', true)->get();
        $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.mr.contacts.create', compact('specialties', 'classifications', 'cities', 'countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:mr_contacts,code',
            'specialty_id' => 'required|exists:contact_specialties,id',
            'classification_id' => 'required|exists:contact_classifications,id',
            'hospital_clinic_name' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'region' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'DOC-' . strtoupper(substr(uniqid(), -6));
        }
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        Contact::create($validated);

        return redirect()->route('admin.mr.contacts.index')
            ->with('success', __('admin.mr.contact_created_successfully', ['name' => $validated['name']]));
    }

    public function show(int $id)
    {
        $contact = Contact::with([
            'specialty',
            'classification',
            'city',
            'country',
            'assignments.cycle',
            'assignments.representative',
            'visits.representative',
            'visits.cycle'
        ])->findOrFail($id);

        return view('admin.mr.contacts.show', compact('contact'));
    }

    public function edit(int $id)
    {
        $contact = Contact::findOrFail($id);
        $specialties = ContactSpecialty::where('is_active', true)->get();
        $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.mr.contacts.edit', compact('contact', 'specialties', 'classifications', 'cities', 'countries'));
    }

    public function update(Request $request, int $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:mr_contacts,code,' . $contact->id,
            'specialty_id' => 'required|exists:contact_specialties,id',
            'classification_id' => 'required|exists:contact_classifications,id',
            'hospital_clinic_name' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'region' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $contact->update($validated);

        return redirect()->route('admin.mr.contacts.index')
            ->with('success', __('admin.mr.contact_updated_successfully', ['name' => $contact->name]));
    }

    public function destroy(int $id)
    {
        $contact = Contact::findOrFail($id);
        $name = $contact->name;
        $contact->delete();

        return redirect()->route('admin.mr.contacts.index')
            ->with('success', __('admin.mr.contact_deleted_successfully', ['name' => $name]));
    }

    /**
     * Resolve Google Maps short URLs or extract coordinates from pasted location links
     */
    public function resolveMapUrl(Request $request)
    {
        $url = trim($request->input('url', ''));
        if (empty($url)) {
            return response()->json(['success' => false, 'message' => 'No URL or coordinates provided'], 422);
        }

        try {
            // Expand short URL if needed (e.g. maps.app.goo.gl or goo.gl/maps)
            if (str_contains($url, 'goo.gl') || str_contains($url, 'maps.app')) {
                $response = \Illuminate\Support\Facades\Http::withoutRedirecting()->head($url);
                $redirectUrl = $response->header('Location');
                if ($redirectUrl) {
                    $url = $redirectUrl;
                }
            }

            // 1. @lat,lng format
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                return response()->json([
                    'success' => true,
                    'lat' => (float) $matches[1],
                    'lng' => (float) $matches[2],
                    'resolved_url' => $url,
                ]);
            }

            // 2. ?q=lat,lng or &q=lat,lng
            if (preg_match('/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                return response()->json([
                    'success' => true,
                    'lat' => (float) $matches[1],
                    'lng' => (float) $matches[2],
                    'resolved_url' => $url,
                ]);
            }

            // 3. ll=lat,lng
            if (preg_match('/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                return response()->json([
                    'success' => true,
                    'lat' => (float) $matches[1],
                    'lng' => (float) $matches[2],
                    'resolved_url' => $url,
                ]);
            }

            // 4. Raw decimal coords "30.044420, 31.235712"
            if (preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)[,\s]+[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $url)) {
                $parts = preg_split('/[,\s]+/', $url);
                return response()->json([
                    'success' => true,
                    'lat' => (float) $parts[0],
                    'lng' => (float) $parts[1],
                    'resolved_url' => $url,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Could not extract coordinates from link',
                'resolved_url' => $url,
            ], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
