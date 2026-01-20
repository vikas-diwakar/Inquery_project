<?php

namespace App\Http\Controllers;

use App\Models\Brochure;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class BrochureController extends Controller
{
    /**
     * Display a listing of brochures
     */
    public function index()
    {
        $brochures = Brochure::where('company_id', auth()->user()->company_id)
            ->with('project')
            ->latest()
            ->paginate(15);
            // dd($brochures);

        return view('brochures.index', compact('brochures'));
    }

    /**
     * Show the form for creating a new brochure
     */
    public function create()
    {
        $projects = Project::where('company_id', auth()->user()->company_id)->get();
        return view('brochures.create', compact('projects'));
    }

    /**
     * Store a newly created brochure
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'brochure_file' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        $project = Project::findOrFail($validated['project_id']);

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
            'project_id' => $validated['project_id'],
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        // Generate QR code URL
        $qrUrl = route('public.brochure.download', ['brochure' => $brochure->id]);
        $brochure->qr_code = $qrUrl;

        $qrImage = QrCode::format('png')
            ->size(300)
            ->generate($qrUrl);

// Save QR image in storage
$qrFileName = 'qrcodes/brochure_' . $brochure->id . '.png';

Storage::disk('public')->put($qrFileName, $qrImage);
        
        $brochure->save();

        return redirect()->route('brochures.index')
            ->with('success', 'Brochure uploaded successfully!');
    }

    /**
     * Download brochure (public)
     */
    public function download(Brochure $brochure)
    {
        if (!Storage::disk('public')->exists($brochure->file_path)) {
            abort(404, 'Brochure not found.');
        }
        return Storage::disk('public')->download($brochure->file_path, $brochure->file_name);
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

        $brochure->delete();

        return redirect()->route('brochures.index')
            ->with('success', 'Brochure deleted successfully!');
    }
}
