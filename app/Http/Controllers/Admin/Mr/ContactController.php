<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use Illuminate\Http\Request;

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

        $contacts = $query->latest()->paginate(15)->withQueryString();
        $specialties = ContactSpecialty::where('is_active', true)->get();
        $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.mr.contacts.index', compact('contacts', 'specialties', 'classifications', 'cities'));
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
