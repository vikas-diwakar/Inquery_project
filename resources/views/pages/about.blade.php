<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <x-seo-meta 
        title="About PropDrip - Real Estate Inquiry & Lead Automation Platform" 
        description="Learn about PropDrip's mission to help real estate builders, developers, and marketing agencies capture buyer inquiries, automate WhatsApp brochures, score lead intent, and increase sales conversion." 
    />

    <!-- Google Fonts: Plus Jakarta Sans & Inter (Non-Render Blocking) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mesh-light min-h-screen text-slate-800 font-sans antialiased selection:bg-indigo-500 selection:text-white">
    <!-- Navbar -->
    <x-public-navbar />

    <!-- Main Content -->
    <main class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <!-- Hero Header -->
            <div class="text-center space-y-4 max-w-3xl mx-auto">
                <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">About PropDrip</span>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight">Empowering Real Estate Developers with Lead Automation</h1>
                <p class="text-slate-600 text-lg leading-relaxed">
                    PropDrip was built to bridge the gap between traditional real estate site visits and modern instant digital communication. We empower builders and sales teams to convert inquiries faster.
                </p>
            </div>

            <!-- Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <article class="p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xl space-y-4">
                    <h2 class="text-2xl font-bold text-slate-900 flex items-center space-x-2">
                        <span>🎯 Our Mission</span>
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        In real estate, lead response time is everything. A buyer inquiring about a residential or commercial property expects instant answers. PropDrip automates digital brochure delivery via WhatsApp the exact second an inquiry form is submitted.
                    </p>
                </article>

                <article class="p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xl space-y-4">
                    <h2 class="text-2xl font-bold text-slate-900 flex items-center space-x-2">
                        <span>🚀 Why Builders Choose PropDrip</span>
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        From instant QR codes on project hoardings to AI-based intent grading (Hot, Warm, Cold) and Round-Robin sales agent allocation, PropDrip gives developers total visibility over their inquiry pipeline.
                    </p>
                </article>
            </div>

            <!-- Impact Numbers -->
            <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-indigo-900 to-slate-900 text-white border border-indigo-800 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center shadow-xl">
                <div class="space-y-1">
                    <span class="text-4xl font-extrabold text-indigo-400">100%</span>
                    <p class="text-xs text-slate-300 font-semibold uppercase tracking-wider">Automated WhatsApp Delivery</p>
                </div>
                <div class="space-y-1">
                    <span class="text-4xl font-extrabold text-indigo-400">&lt; 2 Seconds</span>
                    <p class="text-xs text-slate-300 font-semibold uppercase tracking-wider">Instant Lead Response Time</p>
                </div>
                <div class="space-y-1">
                    <span class="text-4xl font-extrabold text-indigo-400">3x</span>
                    <p class="text-xs text-slate-300 font-semibold uppercase tracking-wider">Higher Lead-to-Visit Conversion</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-200 text-center text-slate-500 text-xs space-y-2">
        <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" class="h-8 w-auto mx-auto mb-2">
        <p>&copy; {{ date('Y') }} PropDrip SaaS. Real Estate Lead & Inquiry Automation Portal.</p>
        <p>Built for Real Estate Builders, Developers & Marketing Agencies.</p>
    </footer>
</body>
</html>
