<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Property Inquiry Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @auth
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                                Property Inquiry SaaS
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                             @if(!session('selected_project_id'))
                            <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Projects
                            </a>
                            @endif
                            @if(session('selected_project_id'))
                                <a href="{{ route('inquiries.index') }}" class="{{ request()->routeIs('inquiries.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Inquiries
                                </a>
                                <a href="{{ route('brochures.index') }}" class="{{ request()->routeIs('brochures.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Brochures
                                </a>
                                <a href="{{ route('forms-qr.index') }}" class="{{ request()->routeIs('forms-qr.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Inquiry Form & QR Code
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Users
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-500">({{ auth()->user()->company->name ?? 'No Company' }})</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <main class="py-6">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('components.confirmation-modal')

  <script>
    let confirmationCallback = null;

    function showConfirmationModal(title, message, callback) {
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');

        modalTitle.textContent = title;
        modalMessage.textContent = message;

        confirmationCallback = callback;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        confirmationCallback = null;
    }

    function confirmAction() {
        if (typeof confirmationCallback === 'function') {
            confirmationCallback(); // 🔥 THIS was never called
        }
        closeConfirmationModal();
    }


    function hideConfirmationModal() {
        console.log('hideConfirmationModal called');
        const modal = document.getElementById('confirmationModal');
        
        if (modal) {
            modal.style.display = 'none';
        }
        confirmationCallback = null;
    }

    // Attach event listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, attaching modal event listeners');
        
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const modal = document.getElementById('confirmationModal');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                console.log('Cancel button clicked');
                hideConfirmationModal();
            });
        } else {
            console.error('Cancel button not found');
        }
        
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                console.log('Confirm button clicked');
                if (confirmationCallback) {
                    confirmationCallback();
                }
                hideConfirmationModal();
            });
        } else {
            console.error('Confirm button not found');
        }
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    console.log('Modal backdrop clicked');
                    hideConfirmationModal();
                }
            });
        } else {
            console.error('Modal not found');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display !== 'none') {
                console.log('Escape key pressed');
                hideConfirmationModal();
            }
        });
    });

    // Test function to check if modal works
    window.testModal = function() {
        showConfirmationModal('Test', 'This is a test modal', function() {
            alert('Confirmed!');
        });
    };
    </script>
</body>
</html>
