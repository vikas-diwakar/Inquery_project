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

        return view('forms-qr.create-inquiry-form', compact('project'));
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

        // Generate inquiry form URL (unique per company and project)
        $inquiryUrl = route('public.inquiry.form', ['project' => $project->id]);
        
        // Generate QR code image and save it
        $qrCodePath = 'qrcodes/inquiry-' . $project->company_id . '-' . $project->id . '.png';
        $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);
        
        // Create directory if it doesn't exist
        $directory = dirname($qrCodeFullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate QR code image
        QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($inquiryUrl, $qrCodeFullPath);

        
        // Save QR code path to project
        $project->inquiry_qr_code = $qrCodePath;
        $project->save();

        return redirect()->route('forms-qr.show-inquiry-qr', $project)
            ->with('success', "QR code generated successfully for project: {$project->name}");
    }

    /**
     * Show inquiry form QR details for selected project
     */
    public function showInquiryQR(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        // Ensure user owns this project
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Generate QR code if it doesn't exist
        if (!$project->inquiry_qr_code || !Storage::disk('public')->exists($project->inquiry_qr_code)) {
            $inquiryUrl = $project->getInquiryFormUrl();
            
            // Generate QR code image and save it
            $qrCodePath = 'qrcodes/inquiry-' . $project->company_id . '-' . $project->id . '.png';
            $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);
            
            // Create directory if it doesn't exist
            $directory = dirname($qrCodeFullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Generate QR code image
            QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->generate($inquiryUrl, $qrCodeFullPath);
            
            $project->inquiry_qr_code = $qrCodePath;
            $project->save();
        }

        return view('forms-qr.show-inquiry-qr', compact('project'));
    }

    /**
     * Download inquiry QR code image
     */
    public function downloadInquiryQR(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        // Ensure user owns this project
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Ensure QR code exists
        if (!$project->inquiry_qr_code || !Storage::disk('public')->exists($project->inquiry_qr_code)) {
            abort(404, 'QR code not found');
        }

        $fileName = 'inquiry-qr-' . $project->getQrCodeIdentifier() . '.png';
        
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

        $brochure->load('project');

        return view('forms-qr.show-brochure-qr', compact('brochure'));
    }
}
