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

        // Save stacking chart toggle preference
        $project->show_stacking_chart = $request->boolean('show_stacking_chart');
        $project->save();

        // Generate inquiry form URL (unique per company and project)
        $inquiryUrl = route('public.inquiry.form', ['project' => $project->id]);
        
        // Generate QR code (SVG) and save it - SVG backend does not require Imagick
        $qrCodePath = 'qrcodes/inquiry-' . $project->company_id . '-' . $project->id . '.svg';
        $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);
        
        // Create directory if it doesn't exist
        $directory = dirname($qrCodeFullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate QR code as SVG (avoids Imagick requirement)
        QrCode::format('svg')
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
            
            // Generate QR code (SVG) and save it
            $qrCodePath = 'qrcodes/inquiry-' . $project->company_id . '-' . $project->id . '.svg';
            $qrCodeFullPath = storage_path('app/public/' . $qrCodePath);
            
            // Create directory if it doesn't exist
            $directory = dirname($qrCodeFullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Generate QR code as SVG
            QrCode::format('svg')
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
        // derive extension from stored path (supports .svg fallback)
        $ext = pathinfo($project->inquiry_qr_code, PATHINFO_EXTENSION) ?: 'svg';
        $fileName = 'inquiry-qr-' . $project->getQrCodeIdentifier() . '.' . $ext;

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
