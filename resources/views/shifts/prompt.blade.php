@extends('layouts.app')

@section('content')
<div class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i data-lucide="lock" class="w-10 h-10"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Shift Required</h2>
        <p class="text-gray-500 mb-8">You must start an active shift to access POS modules and process transactions.</p>
        
        <button onclick="window.dispatchEvent(new CustomEvent('open-start-shift-modal'))" 
                class="w-full py-3.5 px-4 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold rounded-xl shadow-md transition-all flex justify-center items-center gap-2 active:scale-[0.98]">
            <i data-lucide="clock-arrow-up" class="w-5 h-5"></i>
            Start Shift Now
        </button>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-gray-700 font-medium transition-colors">
                Log out instead
            </button>
        </form>
    </div>
</div>
@endsection
