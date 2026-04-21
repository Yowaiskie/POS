@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Manage Rooms</h1>
        <p class="text-slate-600">Monitor and control all KTV room sessions</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @for ($i = 1; $i <= 6; $i++)
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-lg transition-all duration-200 h-full flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Room {{ $i }}</h3>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border bg-slate-100 text-slate-600 border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Available
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2.5 mt-auto">
                <button class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md">
                    Start Session
                </button>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection
