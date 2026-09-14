<?php

namespace App\Http\Controllers;

use App\Models\Brochure;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class BrochureController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of brochures (filtered by selected project)
     */
    public function index()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::with('company')->findOrFail($selectedProjectId);
        
        $brochures = Brochure::where('company_id', auth()->user()->company_id)
            ->where('project_id', $selectedProjectId)
            ->with(['project', 'company'])
            ->latest()
            ->paginate(15);

        return view('brochures.index', compact('brochures', 'project'));
    }

    /**
     * Show the form for creating a new brochure
     */
    public function create()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        return view('brochures.create', compact('project'));
    }

    /**
     * Store a newly created brochure
     */
    public function store(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        
        $validated = $request->validate([
            'brochure_file' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        $project = Project::findOrFail($selectedProjectId);

        // Verify project belongs to company
        if ($project->company_id !== auth()->user()->company_id) {
            return redirect()->back()->with('error', 'Invalid project selected.');
        }

        // Store file
        $file = $request->file('brochure_file');
        $filePath = $file->store('brochures', 'public');
        $fileName = $file->getClientOriginalName();

        $brochure = Brochure::create([
            'company_id' => auth()->user()->company_id,
            'project_id' => $selectedProjectId,
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        // Generate and save QR code using Brochure model method
        $brochure->generateQrCode();

        return redirect()->route('brochures.index')
            ->with('success', 'Brochure uploaded successfully!');
    }

    /**
     * Download brochure (public - ONLY encrypted link allowed)
     */
    public function download($brochure)
    {
        $brochureId = \App\Services\UrlCryptService::decrypt($brochure);
        if (!$brochureId) {
            abort(404, 'Invalid or encrypted brochure link required.');
        }

        $brochure = Brochure::findOrFail($brochureId);

        if (!Storage::disk('public')->exists($brochure->file_path)) {
            abort(404, 'Brochure not found.');
        }

        $brochure->loadMissing(['company', 'project']);

        $companyName = $brochure->company->name ?? $brochure->project->company->name ?? 'Company';
        $projectName = $brochure->project->name ?? 'Project';

        $companySlug = \Illuminate\Support\Str::slug($companyName);
        $projectSlug = \Illuminate\Support\Str::slug($projectName);

        $ext = pathinfo($brochure->file_path, PATHINFO_EXTENSION) ?: pathinfo($brochure->file_name, PATHINFO_EXTENSION) ?: 'pdf';

        // Extract original base filename (without extension)
        $baseOriginal = pathinfo($brochure->file_name, PATHINFO_FILENAME);
        $baseSlug = \Illuminate\Support\Str::slug($baseOriginal);

        if (!empty($baseSlug) && !in_array($baseSlug, ['brochure', 'file', 'document'])) {
            $downloadFileName = "{$companySlug}-{$projectSlug}-{$baseSlug}.{$ext}";
        } else {
            $downloadFileName = "{$companySlug}-{$projectSlug}-brochure.{$ext}";
        }

        return Storage::disk('public')->download($brochure->file_path, $downloadFileName);
    }

    /**
     * Remove the brochure
     */
    public function destroy(Brochure $brochure)
    {
        $this->authorize('delete', $brochure);

        // Delete file
        if (Storage::disk('public')->exists($brochure->file_path)) {
            Storage::disk('public')->delete($brochure->file_path);
        }

        // Delete any generated QR code files for this brochure
        $prefix = 'qrcodes/brochure_' . $brochure->id;
        foreach (Storage::disk('public')->files('qrcodes') as $f) {
            if (str_starts_with($f, $prefix)) {
                Storage::disk('public')->delete($f);
            }
        }

        $brochure->delete();

        return redirect()->route('brochures.index')
            ->with('success', 'Brochure deleted successfully!');
    }
}
