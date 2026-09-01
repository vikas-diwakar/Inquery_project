<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Suspended - {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-transparent h-full flex flex-col justify-center p-3 antialiased">
    <div class="max-w-md w-full mx-auto bg-white rounded-xl shadow-md border border-slate-150 p-8 flex flex-col items-center justify-center text-center my-auto space-y-4">
        <div class="w-16 h-16 rounded-full bg-red-50 border border-red-100 flex items-center justify-center text-red-500 shadow-sm">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Integration Suspended</h3>
            <p class="mt-2 text-xs text-slate-500">The external inquiry widget for <strong>{{ $project->name }}</strong> has been temporarily paused. Please contact the company administrator to verify their subscription status.</p>
        </div>
        
        <!-- Footer Branding -->
        <div class="pt-4 text-[9px] font-semibold text-slate-400 uppercase tracking-widest">
            Powered by Property Inquiry SaaS
        </div>
    </div>
</body>
</html>
