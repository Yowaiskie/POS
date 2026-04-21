@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Manage Rooms</h1>
                <p class="text-slate-600">Monitor and control all KTV room sessions</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm overflow-x-auto">
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Active</span>
                    </div>
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Warning</span>
                    </div>
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Overtime</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $rooms = [
            ['id' => '1', 'name' => 'Room 1', 'status' => 'active', 'timer' => '00:45:00', 'order' => 194],
            ['id' => '2', 'name' => 'Room 2', 'status' => 'warning', 'timer' => '00:08:00', 'order' => 294],
            ['id' => '3', 'name' => 'Room 3', 'status' => 'overtime', 'timer' => '+00:05:00', 'order' => 400],
            ['id' => '4', 'name' => 'Room 4', 'status' => 'available', 'timer' => '00:00:00', 'order' => 0],
            ['id' => '5', 'name' => 'Room 5', 'status' => 'active', 'timer' => '01:30:00', 'order' => 180],
            ['id' => '6', 'name' => 'Room 6', 'status' => 'available', 'timer' => '00:00:00', 'order' => 0],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($rooms as $room)
            @php
                $statusColor = match($room['status']) {
                    'active' => 'emerald',
                    'warning' => 'amber',
                    'overtime' => 'rose',
                    default => 'slate'
                };
            @endphp
            <div class="bg-white rounded-xl border-2 {{ $room['status'] !== 'available' ? "border-$statusColor-200" : 'border-slate-100' }} p-6 shadow-sm hover:shadow-lg transition-all duration-200 h-full flex flex-col group relative overflow-hidden">
                @if($room['status'] !== 'available')
                    <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i data-lucide="mic-2" class="w-16 h-16 text-{{ $statusColor }}-600 -rotate-12"></i>
                    </div>
                @endif

                <div class="flex items-start justify-between mb-4 relative z-10">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $room['name'] }}</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border 
                            @if($room['status'] === 'active') bg-emerald-50 text-emerald-700 border-emerald-200
                            @elseif($room['status'] === 'warning') bg-amber-50 text-amber-700 border-amber-200
                            @elseif($room['status'] === 'overtime') bg-rose-50 text-rose-700 border-rose-200
                            @else bg-slate-50 text-slate-600 border-slate-200 @endif uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full @if($room['status'] === 'active') bg-emerald-500 animate-pulse @elseif($room['status'] === 'warning') bg-amber-500 animate-bounce @elseif($room['status'] === 'overtime') bg-rose-500 animate-ping @else bg-slate-400 @endif"></span>
                            {{ $room['status'] }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 flex flex-col justify-center py-6 bg-slate-50 rounded-xl mb-4 relative z-10">
                    <div class="text-center">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Remaining Time</div>
                        <div class="font-mono text-3xl font-black @if($room['status'] === 'active') text-emerald-600 @elseif($room['status'] === 'warning') text-amber-600 @elseif($room['status'] === 'overtime') text-rose-600 @else text-slate-300 @endif tracking-tighter">
                            {{ $room['timer'] }}
                        </div>
                    </div>
                    @if($room['status'] !== 'available')
                    <div class="mt-4 pt-4 border-t border-slate-200/50 text-center">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Current Bill</div>
                        <div class="text-2xl font-black text-slate-900 tracking-tight">₱{{ number_format($room['order']) }}</div>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-2.5 relative z-10">
                    @if($room['status'] === 'available')
                        <button class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-bold shadow-md shadow-indigo-200">
                            Start Session
                        </button>
                    @else
                        <div class="flex gap-2">
                            <button class="flex-1 px-4 py-2.5 bg-white border-2 border-slate-200 text-slate-700 rounded-lg hover:border-indigo-600 hover:text-indigo-600 active:scale-95 transition-all text-sm font-bold">
                                Orders
                            </button>
                            <button class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 active:scale-95 transition-all text-sm font-bold shadow-md">
                                Extend
                            </button>
                        </div>
                        <button class="w-full px-4 py-2.5 bg-slate-900 text-white rounded-lg hover:bg-black active:scale-95 transition-all text-sm font-bold mt-1">
                            Bill Out
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
