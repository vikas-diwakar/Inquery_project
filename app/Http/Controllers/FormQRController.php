<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Brochure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FormQRController extends Controller
{
    /**
     * Display the Forms & QR Codes management page (for selected project)
     */
    public function index()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        $brochures = Brochure::where('company_id', auth()->user()->company_id)
            ->where('project_id', $selectedProjectId)
            ->latest()
            ->get();

        return view('forms-qr.index', compact('project', 'brochures'));
    }

    /**
     * Show form to create inquiry form QR for selected project
     */
    public function createInquiryForm()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);

        $customFields = $project->customFields()->get();
        return view('forms-qr.create-inquiry-form', compact('project', 'customFields'));
    }

    /**
     * Generate/Regenerate inquiry form QR for selected project
     */
    public function generateInquiryQR(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        // Ensure user owns this project
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Save stacking chart toggle preference
        $project->show_stacking_chart = $request->boolean('show_stacking_chart');
        $project->save();

        // Generate and save QR code
        $project->generateQrCode();

        return redirect()->route('forms-qr.show-inquiry-qr', $project)
            ->with('success', "QR code generated successfully for project: {$project->name}");
    }

    /**
     * Show inquiry form QR details for selected project
     */
    public function showInquiryQR(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::with('company')->findOrFail($selectedProjectId);
        
        // Ensure user owns this project
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Generate QR code if it doesn't exist
        if (!$project->inquiry_qr_code || !Storage::disk('public')->exists($project->inquiry_qr_code)) {
            $project->generateQrCode();
        }

        return view('forms-qr.show-inquiry-qr', compact('project'));
    }

    /**
     * Download inquiry QR code image
     */
    public function downloadInquiryQR(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::with('company')->findOrFail($selectedProjectId);
        
        // Ensure user owns this project
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Ensure QR code exists (generate if missing)
        if (!$project->inquiry_qr_code || !Storage::disk('public')->exists($project->inquiry_qr_code)) {
            $project->generateQrCode();
        }

        $companyName = $project->company->name ?? auth()->user()->company->name ?? 'Company';
        $companySlug = \Illuminate\Support\Str::slug($companyName);
        $projectSlug = \Illuminate\Support\Str::slug($project->name);

        $ext = pathinfo($project->inquiry_qr_code, PATHINFO_EXTENSION) ?: 'svg';
        $fileName = "{$companySlug}-{$projectSlug}-inquiry-qr.{$ext}";

        return Storage::disk('public')->download($project->inquiry_qr_code, $fileName);
    }

    /**
     * Show brochure QR management (for selected project)
     */
    public function brochureQR()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        $brochures = Brochure::where('company_id', auth()->user()->company_id)
            ->where('project_id', $selectedProjectId)
            ->latest()
            ->get();

        return view('forms-qr.brochure-qr', compact('brochures', 'project'));
    }

    /**
     * Show brochure QR details
     */
    public function showBrochureQR(Brochure $brochure)
    {
        // Ensure user owns this brochure
        if ($brochure->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Ensure QR code exists
        if (!$brochure->qr_code || !Storage::disk('public')->exists('qrcodes/brochure_' . $brochure->id . '.svg')) {
            $brochure->generateQrCode();
        }

        $brochure->load(['project', 'company']);

        return view('forms-qr.show-brochure-qr', compact('brochure'));
    }

    /**
     * Store a new custom form field for this project
     */
    public function storeCustomField(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);

        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,number,select,textarea',
            'field_options_raw' => 'nullable|string|max:1000',
            'placeholder' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);

        $baseSlug = \Illuminate\Support\Str::slug($validated['field_label'], '_');
        if (empty($baseSlug)) {
            $baseSlug = 'field_' . time();
        }

        $options = null;
        if ($validated['field_type'] === 'select' && !empty($validated['field_options_raw'])) {
            $options = array_values(array_filter(array_map('trim', explode(',', $validated['field_options_raw']))));
        }

        $nextOrder = (int) $project->customFields()->max('sort_order') + 1;

        \App\Models\InquiryCustomField::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'field_label' => $validated['field_label'],
            'field_name' => $baseSlug,
            'field_type' => $validated['field_type'],
            'field_options' => $options,
            'placeholder' => $validated['placeholder'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => true,
            'sort_order' => $nextOrder,
        ]);

        return redirect()->back()->with('success', "Custom field '{$validated['field_label']}' added to inquiry form!");
    }

    /**
     * Delete a custom form field
     */
    public function deleteCustomField(\App\Models\InquiryCustomField $field)
    {
        if ($field->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        $name = $field->field_label;
        $field->delete();

        return redirect()->back()->with('success', "Custom field '{$name}' removed from inquiry form.");
    }

    /**
     * Toggle active/inactive status of a custom field
     */
    public function toggleCustomField(\App\Models\InquiryCustomField $field)
    {
        if ($field->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        $field->is_active = !$field->is_active;
        $field->save();

        $status = $field->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Field '{$field->field_label}' has been {$status}.");
    }
}
