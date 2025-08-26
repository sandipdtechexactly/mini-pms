@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 p-8 bg-white rounded-xl shadow-md">
        <div class="text-center" style="color: red;">
            <div class="mx-auto flex items-center justify-center h-16 w-16 text-red-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">
                403 - Access Denied
            </h2>
            <p class="mt-2 text-gray-600">
                You don't have permission to access this page.
            </p>
        </div>
        
        <div class="mt-6">
            <a href="{{ url('/dashboard') }}" class="w-full flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 transform hover:scale-105">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="whitespace-nowrap" style="color: #373c44">Return to Dashboard</span>
            </a>
        </div>
        
        @if(app()->bound('sentry') && !empty(Sentry::getLastEventId()))
        <div class="mt-4 text-center text-xs text-gray-500">
            <p>Error ID: {{ Sentry::getLastEventId() }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
