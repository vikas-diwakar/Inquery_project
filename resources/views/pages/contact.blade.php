<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <x-seo-meta 
        title="Contact Us - PropDrip Real Estate Inquiry SaaS" 
        description="Have questions about PropDrip for your real estate projects? Get in touch with our team for support, product demos, or enterprise inquiries." 
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
            <!-- Header -->
            <div class="text-center space-y-4 max-w-3xl mx-auto">
                <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">Get In Touch</span>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight">Contact Our Support & Sales Team</h1>
                <p class="text-slate-600 text-lg leading-relaxed">
                    Have questions about automating your real estate project inquiries or setting up WhatsApp brochures? We are here to help.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Details -->
                <div class="space-y-8 bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xl">
                    <h2 class="text-2xl font-bold text-slate-900">Contact Information</h2>
                    
                    <div class="space-y-6 text-sm">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 text-xl font-bold">
                                ✉️
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Email Us</h3>
                                <p class="text-slate-600">support@propdrip.in</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 text-xl font-bold">
                                📞
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Call Support</h3>
                                <p class="text-slate-600">+91 98765 43210</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 text-xl font-bold">
                                ⏰
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Support Hours</h3>
                                <p class="text-slate-600">Monday - Saturday (9:00 AM - 7:00 PM IST)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xl space-y-6">
                    <h2 class="text-2xl font-bold text-slate-900">Send Us a Message</h2>

                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center space-x-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                            <p class="font-bold">Please correct the following errors:</p>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Your Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="John Doe" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-sm transition-all">
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="john@company.com" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-sm transition-all">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Subject <span class="text-rose-500">*</span></label>
                            <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" placeholder="Inquiry about PropDrip for Real Estate Project" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-sm transition-all">
                        </div>

                        <div class="space-y-2">
                            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Message <span class="text-rose-500">*</span></label>
                            <textarea id="message" name="message" rows="4" required placeholder="Tell us how we can help you..." class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-sm transition-all">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm transition-all shadow-md shadow-indigo-500/20">
                            Send Message →
                        </button>
                    </form>
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
