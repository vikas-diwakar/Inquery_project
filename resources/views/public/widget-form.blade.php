<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry - {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Modern Micro-animations & custom classes */
        .input-accent:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .animate-check {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: draw 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards 0.2s;
        }
        @keyframes draw {
            to { stroke-dashoffset: 0; }
        }
    </style>
</head>
<body class="bg-transparent h-full flex flex-col justify-center p-3 antialiased">
    <div class="max-w-md w-full mx-auto bg-white rounded-xl shadow-md border border-slate-150 overflow-hidden flex flex-col h-full justify-between">
        @if(session('success'))
            <!-- Success Thank You Card -->
            <div class="p-8 flex flex-col items-center justify-center text-center my-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" class="animate-check" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Thank You!</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ session('success') }}</p>
                </div>
                <div class="pt-4">
                    <a href="{{ request()->fullUrl() }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3m0 0l3 3m-3-3v8"/></svg>
                        Submit another response
                    </a>
                </div>
            </div>
        @else
            <!-- Main Inquiry Form -->
            <div class="p-6">
                <!-- Header -->
                <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-3">
                    @if($project->logo)
                        <img src="{{ asset('storage/' . $project->logo) }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover bg-slate-50 border border-slate-100 shadow-sm">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold shadow-sm">
                            {{ substr($project->name, 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-base font-bold text-slate-900 leading-tight">{{ $project->name }}</h2>
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Inquiry Registration</span>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-xs mb-4">
                        <ul class="list-disc list-inside space-y-0.5 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.inquiry.widget.store', ['token' => $project->lead_token]) }}" method="POST" class="space-y-3">
                    @csrf
                    <!-- Preserve custom source tracking from query parameter -->
                    <input type="hidden" name="source" value="{{ request()->input('source', 'website_widget') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Full Name -->
                        <div class="sm:col-span-2">
                            <label for="customer_name" class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}" placeholder="John Doe"
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 placeholder-slate-400 focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" required value="{{ old('phone') }}" placeholder="+91 98765 43210"
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 placeholder-slate-400 focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="john@example.com"
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 placeholder-slate-400 focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>

                        <!-- Unit Options dropdown -->
                        <div>
                            <label for="selected_unit_option_id" class="block text-xs font-bold text-slate-700 mb-1">Unit Requirement</label>
                            <select name="selected_unit_option_id" id="selected_unit_option_id"
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 focus:border-indigo-500 focus:ring-0 transition-all">
                                <option value="">Select Option</option>
                                @foreach($unitOptions as $option)
                                    <option value="{{ $option->id }}" {{ old('selected_unit_option_id') == $option->id ? 'selected' : '' }}>
                                        {{ $option->option_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Budget -->
                        <div>
                            <label for="budget" class="block text-xs font-bold text-slate-700 mb-1">Max Budget (₹)</label>
                            <input type="number" step="0.01" name="budget" id="budget" value="{{ old('budget') }}" placeholder="e.g. 7500000"
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 placeholder-slate-400 focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>

                        <!-- Message -->
                        <div class="sm:col-span-2">
                            <label for="message" class="block text-xs font-bold text-slate-700 mb-1">Message / Notes</label>
                            <textarea name="message" id="message" rows="2" placeholder="Tell us more about your requirement..."
                                class="input-accent block w-full rounded-lg border-slate-200 text-slate-900 text-sm px-3 py-2 placeholder-slate-400 focus:border-indigo-500 focus:ring-0 transition-all">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg text-sm font-bold shadow transition-all">
                            Submit Inquiry
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Footer Branding -->
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-2.5 text-center">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider flex items-center justify-center gap-1.5"><img src="{{ asset('images/propdrip-logo.png') }}" class="h-3.5 w-auto inline" alt="PropDrip"> Powered by PropDrip</span>
            </div>
        @endif
    </div>
</body>
</html>
