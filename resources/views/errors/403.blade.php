@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.public')

@section('title', 'Access Denied')
@section('page-title', 'Access Denied')

@section('content')
<div class="flex items-center justify-center min-h-96">
    <div class="text-center max-w-md">
        <div class="text-8xl mb-6">🚫</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-3">403</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-3">Access Denied</h2>
        <p class="text-gray-500 mb-6">You don't have permission to view this page. Contact your administrator if you believe this is an error.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 bg-blue-700 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
                Back to Dashboard
            </a>
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
