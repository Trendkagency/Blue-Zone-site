<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateInterview;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobOffer;
use App\Models\JobVacancy;
use App\Models\Location;
use App\Models\Position;
use App\Services\Hr\EmployeeNumberService;
use App\Services\Hr\RecruitmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    protected RecruitmentService $recruitmentService;

    public function __construct(RecruitmentService $recruitmentService)
    {
        $this->recruitmentService = $recruitmentService;
    }

    public function index(): View
    {
        $vacanciesCount = JobVacancy::where('status', 'published')->count();
        $candidatesCount = Candidate::count();
        $interviewsCount = CandidateInterview::where('status', 'scheduled')->count();
        $offersCount = JobOffer::where('status', 'sent')->count();

        $recentCandidates = Candidate::with('vacancy')->latest()->take(10)->get();
        $activeVacancies = JobVacancy::with(['department', 'position', 'location'])->withCount('candidates')->latest()->take(5)->get();

        return view('admin.hr.recruitment.index', compact(
            'vacanciesCount',
            'candidatesCount',
            'interviewsCount',
            'offersCount',
            'recentCandidates',
            'activeVacancies'
        ));
    }

    public function vacancies(): View
    {
        $vacancies = JobVacancy::with(['department', 'position', 'location'])->withCount('candidates')->latest()->paginate(15);
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();

        return view('admin.hr.recruitment.vacancies', compact('vacancies', 'departments', 'positions', 'locations'));
    }

    public function storeVacancy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'title_en' => ['required', 'string', 'max:150'],
            'title_ar' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'openings' => ['required', 'integer', 'min:1'],
            'employment_type' => ['required', 'string'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'closing_at' => ['nullable', 'date'],
        ]);

        $validated['opened_at'] = now()->toDateString();
        $validated['status'] = 'published';

        JobVacancy::create($validated);

        return back()->with('success', __('hr.vacancy_created_successfully', ['default' => 'Job vacancy created successfully.']));
    }

    public function candidates(Request $request): View
    {
        $query = Candidate::with(['vacancy', 'convertedEmployee']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('candidate_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($vacId = $request->input('job_vacancy_id')) {
            $query->where('job_vacancy_id', $vacId);
        }

        $candidates = $query->latest()->paginate(15)->withQueryString();
        $vacancies = JobVacancy::where('status', 'published')->get();

        return view('admin.hr.recruitment.candidates', compact('candidates', 'vacancies'));
    }

    public function storeCandidate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_vacancy_id' => ['nullable', 'exists:job_vacancies,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'source' => ['nullable', 'string'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'education' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $candidateNumber = EmployeeNumberService::getInstance()->generateCandidateNumber();
        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('hr/resumes', 'public');
        }

        Candidate::create(array_merge($validated, [
            'candidate_number' => $candidateNumber,
            'cv_path' => $cvPath,
            'status' => 'new',
        ]));

        return back()->with('success', __('hr.candidate_added_successfully', ['default' => 'Candidate application registered successfully.']));
    }

    public function convertCandidate(Request $request, int $id): RedirectResponse
    {
        $candidate = Candidate::with('vacancy')->findOrFail($id);

        $validated = $request->validate([
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'hire_date' => ['required', 'date'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);

        $employee = $this->recruitmentService->convertCandidateToEmployee($candidate, $validated, auth()->id());

        return redirect()->route('admin.hr.employees.show', $employee->id)
            ->with('success', __('hr.candidate_converted_successfully', ['default' => 'Candidate hired and converted to Employee!']));
    }

    public function interviews(Request $request): View
    {
        $query = CandidateInterview::with(['candidate.vacancy', 'interviewer']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $interviews = $query->latest('scheduled_at')->paginate(15)->withQueryString();
        $candidates = Candidate::whereIn('status', ['shortlisted', 'interview', 'technical_test'])->get();
        $interviewers = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.recruitment.interviews', compact('interviews', 'candidates', 'interviewers'));
    }

    public function storeInterview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'interviewer_employee_id' => ['nullable', 'exists:employees,id'],
            'interview_type' => ['required', 'string'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->recruitmentService->scheduleInterview($validated['candidate_id'], $validated);

        return back()->with('success', __('hr.interview_scheduled_successfully', ['default' => 'Interview scheduled successfully.']));
    }

    public function offers(Request $request): View
    {
        $query = JobOffer::with(['candidate', 'position', 'department']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(15)->withQueryString();
        $candidates = Candidate::whereIn('status', ['interview', 'offer', 'technical_test'])->get();
        $positions = Position::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.hr.recruitment.offers', compact('offers', 'candidates', 'positions', 'departments'));
    }

    public function storeOffer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_type' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'probation_period' => ['nullable', 'string'],
            'offer_expiry_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->recruitmentService->issueJobOffer($validated['candidate_id'], $validated, auth()->id());

        return back()->with('success', __('hr.job_offer_issued_successfully', ['default' => 'Job offer created and sent.']));
    }
}
