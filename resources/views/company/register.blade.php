@extends('layouts.app')

@section('title', 'Company Registration')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Register Your Company</h2>
        
        <form action="{{ route('company.register') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Company Information</h3>
                </div>
                
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                    <input type="text" name="company_name" id="company_name" required value="{{ old('company_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email *</label>
                    <input type="email" name="company_email" id="company_email" required value="{{ old('company_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="company_phone" class="block text-sm font-medium text-gray-700">Company Phone</label>
                    <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="company_logo" class="block text-sm font-medium text-gray-700">Company Logo</label>
                    <input type="file" name="company_logo" id="company_logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                
                <div class="md:col-span-2">
                    <label for="company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                    <textarea name="company_address" id="company_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('company_address') }}</textarea>
                </div>
                
                <!-- Admin User Information -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Admin User Information</h3>
                </div>
                
                <div>
                    <label for="admin_name" class="block text-sm font-medium text-gray-700">Admin Name *</label>
                    <input type="text" name="admin_name" id="admin_name" required value="{{ old('admin_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email *</label>
                    <input type="email" name="admin_email" id="admin_email" required value="{{ old('admin_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="admin_password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="admin_password" id="admin_password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="w-full md:w-auto px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register Company
                </button>
                <a href="{{ route('login') }}" class="ml-4 text-sm text-indigo-600 hover:text-indigo-500">
                    Already have an account? Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
