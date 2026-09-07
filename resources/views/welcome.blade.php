<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <x-seo-meta 
        title="PropDrip - Real Estate Lead Management & Property Inquiry SaaS" 
        description="PropDrip is the ultimate Real Estate Builder & Agency SaaS platform to capture buyer inquiries, generate project QR codes, automate instant WhatsApp brochures, score lead intent, and nurture leads." 
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

    <!-- Hero Section -->
    <main>
        <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-28 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span>Real Estate Lead Automation Platform</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight max-w-5xl mx-auto leading-tight">
                    Automate Property Inquiry Leads & <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800 bg-clip-text text-transparent">Instant WhatsApp Brochures</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-lg sm:text-xl text-slate-600 max-w-3xl mx-auto font-medium leading-relaxed">
                    Capture buyer inquiries from site QR codes & web widgets, grade lead intent automatically with AI, deliver instant WhatsApp brochures, and assign leads via Round-Robin.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="{{ route('company.register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-base transition-all shadow-xl shadow-indigo-600/30 text-center hover:scale-105">
                        Start 3-Month Free Trial →
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 font-extrabold text-base transition-all shadow-md text-center">
                        Sign In to Portal
                    </a>
                </div>

                <!-- Feature Highlights -->
                <div class="pt-10 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto text-left">
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md space-y-1.5">
                        <span class="text-indigo-600 font-extrabold text-base flex items-center gap-2">📱 Site QR Forms</span>
                        <p class="text-xs text-slate-500 font-medium">Dynamic inquiry forms & QR codes for project sites.</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md space-y-1.5">
                        <span class="text-indigo-600 font-extrabold text-base flex items-center gap-2">💬 WhatsApp Drip</span>
                        <p class="text-xs text-slate-500 font-medium">Instant brochure delivery & automated follow-ups.</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md space-y-1.5">
                        <span class="text-indigo-600 font-extrabold text-base flex items-center gap-2">🔥 AI Lead Scoring</span>
                        <p class="text-xs text-slate-500 font-medium">Auto-grade intent as Hot, Warm, or Cold leads.</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md space-y-1.5">
                        <span class="text-indigo-600 font-extrabold text-base flex items-center gap-2">🎯 Round-Robin</span>
                        <p class="text-xs text-slate-500 font-medium">Equally allocate leads across your sales reps.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Showcase Section -->
        <section class="py-20 bg-white border-t border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <div class="text-center space-y-4 max-w-3xl mx-auto">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">Built for Real Estate Builders</span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">Everything Developers Need to Convert Buyers</h2>
                    <p class="text-slate-600 text-base">Eliminate manual response delays. PropDrip handles lead capture, instant brochure delivery, and agent assignment automatically.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <article class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-indigo-500/40 hover:shadow-xl transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                            📲
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Smart Inquiry Forms & QR Codes</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Share unique project inquiry links or print high-res QR codes for billboards, site offices, and digital ads.
                        </p>
                    </article>

                    <!-- Feature 2 -->
                    <article class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-indigo-500/40 hover:shadow-xl transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                            🤖
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">AI Lead Intent Scoring</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Automatically evaluate buyer budgets, unit preferences, and contact details to grade leads as Hot, Warm, or Cold.
                        </p>
                    </article>

                    <!-- Feature 3 -->
                    <article class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-indigo-500/40 hover:shadow-xl transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                            ⚡
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Lead Drip Nurture Sequences</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Keep buyer interest active with scheduled follow-up drip messages sending location maps, floor plans, and price updates.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA Banner -->
        <section class="py-16 bg-slate-900 text-white relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 text-center space-y-6 relative z-10">
                <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" class="h-14 w-auto mx-auto rounded-2xl p-1 bg-white shadow-xl">
                <h2 class="text-3xl sm:text-4xl font-extrabold">Ready to Boost Your Project Conversions?</h2>
                <p class="text-slate-300 text-lg max-w-2xl mx-auto">Join developers and real estate marketing agencies using PropDrip for automated lead response.</p>
                <div class="pt-2">
                    <a href="{{ route('company.register') }}" class="inline-flex items-center px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-base transition-all shadow-xl shadow-indigo-600/40 hover:scale-105">
                        Register Your Agency / Company →
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-200 text-center text-slate-500 text-xs space-y-2">
        <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" class="h-8 w-auto mx-auto mb-2">
        <p>&copy; {{ date('Y') }} PropDrip SaaS. Real Estate Lead & Inquiry Automation Portal.</p>
        <p>Built for Real Estate Builders, Developers & Marketing Agencies.</p>
    </footer>
</body>
</html>
