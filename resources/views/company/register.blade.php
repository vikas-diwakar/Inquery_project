@extends('layouts.app')

@section('title', 'Register Company - Property Inquiry SaaS')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        <!-- Top Hero Header Banner -->
        <div class="bg-mesh p-8 sm:p-10 text-white relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="inline-flex items-center space-x-2 bg-indigo-500/20 backdrop-blur-md border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-semibold text-indigo-200">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>3-Month Free Trial Included</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Register Your Real Estate Company</h1>
                    <p class="text-sm text-slate-300">Set up your builder workspace & admin credentials in seconds.</p>
                </div>

                <div class="hidden md:flex items-center justify-center h-16 w-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-inner">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Registration Form Body -->
        <form action="{{ route('company.register') }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8">
            @csrf
            
            <!-- SECTION 1: Company Information -->
            <div class="space-y-5">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                    <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">1</div>
                    <h2 class="text-lg font-bold text-slate-900">Company & Builder Details</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Company Name -->
                    <div class="space-y-1.5">
                        <label for="company_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Company Name <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <input type="text" name="company_name" id="company_name" required value="{{ old('company_name') }}" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="Apex Infrastructure Ltd">
                        </div>
                    </div>
                    
                    <!-- Company Email -->
                    <div class="space-y-1.5">
                        <label for="company_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Company Email <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                                </svg>
                            </div>
                            <input type="email" name="company_email" id="company_email" required value="{{ old('company_email') }}" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="contact@apexinfra.com">
                        </div>
                    </div>
                    
                    <!-- Company Phone -->
                    <div class="space-y-1.5">
                        <label for="company_phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phone Number</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone') }}" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="+1 (555) 000-0000">
                        </div>
                    </div>
                    
                    <!-- Company Logo Upload -->
                    <div class="space-y-1.5">
                        <label for="company_logo" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Company Logo</label>
                        <input type="file" name="company_logo" id="company_logo" accept="image/*" 
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer transition-all">
                    </div>
                    
                    <!-- Company Address -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="company_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Office Address</label>
                        <textarea name="company_address" id="company_address" rows="2" 
                            class="block w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                            placeholder="Suite 400, Financial Center, City">{{ old('company_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Admin User Information -->
            <div class="space-y-5 pt-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                    <div class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">2</div>
                    <h2 class="text-lg font-bold text-slate-900">Admin Account Credentials</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Admin Name -->
                    <div class="space-y-1.5">
                        <label for="admin_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Admin Full Name <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="admin_name" id="admin_name" required value="{{ old('admin_name') }}" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="John Doe">
                        </div>
                    </div>
                    
                    <!-- Admin Email -->
                    <div class="space-y-1.5">
                        <label for="admin_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Admin Email <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="admin_email" id="admin_email" required value="{{ old('admin_email') }}" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="admin@apexinfra.com">
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label for="admin_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="admin_password" id="admin_password" required 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="••••••••">
                        </div>
                    </div>
                    
                    <!-- Password Confirmation -->
                    <div class="space-y-1.5">
                        <label for="admin_password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Confirm Password <span class="text-rose-500">*</span></label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" required 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Submission Action Bar -->
            <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <button type="submit" class="w-full sm:w-auto btn-gradient shadow-glow text-white font-semibold py-3 px-8 rounded-xl text-sm transition-all duration-200 flex items-center justify-center space-x-2">
                    <span>Create Company Workspace</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
                <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 text-center sm:text-right">
                    Already registered? Sign in to your workspace &rarr;
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

