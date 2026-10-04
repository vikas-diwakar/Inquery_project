<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use App\Models\ProjectUnitOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SocialMediaController extends Controller
{
    /**
     * Display the Social Media dashboard for the selected project (Admin only, project-wise)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access: Social Media lead management is restricted to Admin users only.');
        }

        $companyProjects = Project::where('company_id', $user->company_id)
            ->orderBy('name')
            ->get();

        if ($companyProjects->isEmpty()) {
            return redirect()->route('projects.create')
                ->with('error', 'Please create a project first to configure social media lead capture.');
        }

        // Determine active project: query param > session > first company project
        $targetProjectId = $request->query('project_id', session('selected_project_id'));
        $project = $companyProjects->firstWhere('id', $targetProjectId) ?? $companyProjects->first();

        // Sync project in session
        session(['selected_project_id' => $project->id]);

        // Ensure token exists
        if (empty($project->lead_token)) {
            $project->lead_token = Str::random(32);
            $project->save();
        }

        // Get recent external inquiries (widget, webhook, facebook, instagram) for this project
        $recentLeads = Inquiry::where('project_id', $project->id)
            ->whereIn('type', ['widget', 'webhook'])
            ->latest()
            ->take(15)
            ->get();

        return view('social-media.index', compact('project', 'companyProjects', 'recentLeads'));
    }

    /**
     * Select a specific project and redirect to its social media leads
     */
    public function showProject(Project $project)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access: Social Media leads are restricted to Admin users only.');
        }

        if ($project->company_id !== $user->company_id) {
            abort(403, 'Unauthorized access: Project does not belong to your company.');
        }

        session(['selected_project_id' => $project->id]);

        return redirect()->route('social-media.index');
    }

    /**
     * Regenerate the lead token for a project
     */
    public function regenerateToken(Request $request, Project $project)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access: Social Media leads are restricted to Admin users only.');
        }

        // Check permissions: project belongs to user's company
        if ($project->company_id !== $user->company_id) {
            abort(403, 'Unauthorized access.');
        }

        $project->lead_token = Str::random(32);
        $project->save();

        session(['selected_project_id' => $project->id]);

        return redirect()->route('social-media.index')
            ->with('success', "Social Media lead token for '{$project->name}' regenerated successfully!");
    }

    /**
     * Show the embeddable iframe inquiry form widget
     */
    public function showWidget($token)
    {
        $project = Project::where('lead_token', $token)->first();
        
        if (!$project) {
            abort(404, 'Invalid social media / lead token.');
        }

        $unitOptions = $project->enabledUnitOptions;

        return view('public.widget-form', compact('project', 'unitOptions'));
    }

    /**
     * Store inquiry submitted from the embeddable widget
     */
    public function storeWidget(Request $request, $token)
    {
        $project = Project::where('lead_token', $token)->first();

        if (!$project) {
            return response()->json(['error' => 'Invalid social media / lead token.'], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($project) {
                    if (Inquiry::isPhoneDuplicateForProject($project->id, $value)) {
                        $fail('An inquiry with this mobile number has already been submitted for this project.');
                    }
                },
            ],
            'email' => 'nullable|email|max:255',
            'budget' => 'nullable|numeric|min:0',
            'selected_unit_option_id' => 'nullable|exists:project_unit_options,id',
            'message' => 'nullable|string',
        ]);

        $flatType = null;
        if (!empty($validated['selected_unit_option_id'])) {
            $option = ProjectUnitOption::find($validated['selected_unit_option_id']);
            if ($option) {
                $flatType = $option->option_name;
            }
        }

        Inquiry::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'flat_type' => $flatType,
            'selected_unit_option_id' => $validated['selected_unit_option_id'] ?? null,
            'message' => $validated['message'] ?? null,
            'source' => $request->input('source', 'website_widget'),
            'type' => 'widget',
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Thank you! Your inquiry has been submitted successfully.');
    }

    /**
     * REST Webhook API to submit leads from any social media (Zapier/Make/Facebook/Instagram) or custom forms
     */
    public function handleWebhook(Request $request, $token)
    {
        $project = Project::where('lead_token', $token)->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid social media / webhook token.'
            ], 404);
        }

        $payload = $request->all();
        Log::info('Social Media lead webhook payload received:', [
            'project_id' => $project->id,
            'token' => $token,
            'payload' => $payload
        ]);

        // Smart Field Mapping
        $customerName = $this->findPayloadValue($payload, ['customer_name', 'name', 'full_name', 'fullname', 'first_name', 'lead_name']);
        
        // If first_name was found and we also have a last_name, combine them
        if (isset($payload['first_name']) && isset($payload['last_name']) && $customerName === $payload['first_name']) {
            $customerName = $payload['first_name'] . ' ' . $payload['last_name'];
        }

        $phone = $this->findPayloadValue($payload, ['phone', 'phone_number', 'mobile', 'contact', 'contact_number', 'phone_no', 'tel']);
        $email = $this->findPayloadValue($payload, ['email', 'email_address', 'mail']);
        $budget = $this->findPayloadValue($payload, ['budget', 'price', 'investment', 'max_budget']);
        $flatTypeStr = $this->findPayloadValue($payload, ['flat_type', 'flat', 'unit', 'unit_type', 'property_type', 'requirement', 'configuration']);
        $message = $this->findPayloadValue($payload, ['message', 'notes', 'comments', 'description', 'remarks']);
        $source = $this->findPayloadValue($payload, ['source', 'lead_source', 'platform']) ?? 'Social Media Webhook';

        // Validation checks
        if (empty($customerName)) {
            return response()->json([
                'success' => false,
                'error' => 'Validation error: The customer name field is required. Checked keys: name, customer_name, full_name.'
            ], 422);
        }

        if (empty($phone)) {
            return response()->json([
                'success' => false,
                'error' => 'Validation error: The phone number field is required. Checked keys: phone, mobile, phone_number, contact.'
            ], 422);
        }

        // Clean and validate budget
        if (!empty($budget)) {
            $budget = floatval(preg_replace('/[^\d.]/', '', $budget));
        } else {
            $budget = null;
        }

        // Auto-match flat type to unit option ID
        $unitOptionId = null;
        if (!empty($flatTypeStr)) {
            $option = ProjectUnitOption::where('project_id', $project->id)
                ->where('option_name', 'like', trim($flatTypeStr))
                ->first();

            if ($option) {
                $unitOptionId = $option->id;
                $flatTypeStr = $option->option_name;
            }
        }

        // Create the inquiry
        $inquiry = Inquiry::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'customer_name' => substr(trim($customerName), 0, 255),
            'phone' => substr(trim($phone), 0, 20),
            'email' => $email ? substr(trim($email), 0, 255) : null,
            'budget' => $budget,
            'flat_type' => $flatTypeStr ? substr(trim($flatTypeStr), 0, 50) : null,
            'selected_unit_option_id' => $unitOptionId,
            'message' => $message,
            'source' => substr(trim($source), 0, 100),
            'type' => 'webhook',
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully.',
            'lead_id' => $inquiry->id,
            'mapped_data' => [
                'customer_name' => $inquiry->customer_name,
                'phone' => $inquiry->phone,
                'email' => $inquiry->email,
                'budget' => $inquiry->budget,
                'flat_type' => $inquiry->flat_type,
                'unit_option_matched' => $unitOptionId ? true : false,
                'source' => $inquiry->source,
            ]
        ], 201);
    }

    /**
     * Helper to find value in payload based on array of potential keys (case-insensitive)
     */
    private function findPayloadValue(array $payload, array $keys)
    {
        // Check exact match first
        foreach ($keys as $key) {
            if (isset($payload[$key])) {
                return $payload[$key];
            }
        }

        // Check case-insensitive match next
        foreach ($payload as $payloadKey => $value) {
            $lowerPayloadKey = strtolower($payloadKey);
            foreach ($keys as $key) {
                if ($lowerPayloadKey === strtolower($key)) {
                    return $value;
                }
            }
        }

        return null;
    }
}
