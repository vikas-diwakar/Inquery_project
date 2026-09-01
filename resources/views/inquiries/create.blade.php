@extends('layouts.app')

@section('title', 'Add Inquiry - ' . $project->name)

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="mb-6">
        <a href="{{ route('inquiries.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Inquiries</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        <div class="bg-slate-900 p-6 sm:p-8 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1 relative z-10">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Add Manual Inquiry</h1>
                <p class="text-xs sm:text-sm text-slate-300">Project: <span class="font-semibold text-indigo-300">{{ $project->name }}</span></p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center text-indigo-300 relative z-10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('inquiries.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div class="space-y-5">
                <div class="space-y-1.5">
                    <label for="customer_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Customer Name <span class="text-rose-500">*</span></label>
                    <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="Enter customer's full name">
                </div>

                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phone Number <span class="text-rose-500">*</span></label>
                    <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="Enter phone number">
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="Enter email address">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="budget" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Budget (₹)</label>
                        <input id="budget" name="budget" type="number" step="0.01" value="{{ old('budget') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="e.g. 250000">
                    </div>

                    <div class="space-y-1.5">
                        <label for="selected_unit_option_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Property Unit Type</label>
                        <select id="selected_unit_option_id" name="selected_unit_option_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                            <option value="">-- Select Unit Type --</option>
                            @foreach($project->enabledUnitOptions as $option)
                                <option value="{{ $option->id }}" {{ old('selected_unit_option_id') == $option->id ? 'selected' : '' }}>
                                    {{ $option->option_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="message" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Customer Message / Notes</label>
                    <textarea id="message" name="message" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="Additional customer requirements...">{{ old('message') }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label for="assigned_to" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assign To Executive</label>
                    <select id="assigned_to" name="assigned_to" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                        <option value="">-- Unassigned --</option>
                        @foreach($projectUsers as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('inquiries.index') }}" class="px-6 py-2.5 font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/20 transition-all flex items-center space-x-2">
                    <span>Create Inquiry</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
