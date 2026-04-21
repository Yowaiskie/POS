@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2"> Dashboard Overview</h1>
        <p class="text-slate-600">Real-time monitoring and analytics for your KTV operations</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10">
        <!-- Active Rooms -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Active Rooms</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold text-emerald-600">3</div>
        </div>

        <!-- Available Rooms -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Available Rooms</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold text-sky-600">3</div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Total Sales Today</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="philippine-peso" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold text-pink-600">₱888</div>
        </div>
    </div>

    <!-- Active Sessions Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h2 class="text-xl md:text-2xl font-bold text-slate-900">Active Sessions</h2>
            <a href="{{ route('rooms.index') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 active:scale-95 transition-all shadow-md w-full sm:w-auto font-medium text-center">
                View All Rooms
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Room 1 -->
            <div class="bg-white border border-slate-200 border-l-4 border-l-emerald-500 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-lg font-semibold text-slate-900">Room 1</h3>
                    <div class="text-xs text-slate-500 capitalize px-2 py-1 bg-slate-50 rounded border border-slate-100">active</div>
                </div>
                <div class="flex items-center justify-center py-4 bg-slate-50 rounded-lg mb-3">
                    <span class="font-mono text-2xl font-bold text-emerald-600">00:45:00</span>
                </div>
                <div class="text-right font-semibold text-pink-600">₱194</div>
            </div>

            <!-- Room 2 -->
            <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-lg font-semibold text-slate-900">Room 2</h3>
                    <div class="text-xs text-slate-500 capitalize px-2 py-1 bg-slate-50 rounded border border-slate-100">warning</div>
                </div>
                <div class="flex items-center justify-center py-4 bg-slate-50 rounded-lg mb-3">
                    <span class="font-mono text-2xl font-bold text-amber-600">00:08:00</span>
                </div>
                <div class="text-right font-semibold text-pink-600">₱294</div>
            </div>

            <!-- Room 3 -->
            <div class="bg-white border border-slate-200 border-l-4 border-l-rose-500 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-lg font-semibold text-slate-900">Room 3</h3>
                    <div class="text-xs text-slate-500 capitalize px-2 py-1 bg-slate-50 rounded border border-slate-100">overtime</div>
                </div>
                <div class="flex items-center justify-center py-4 bg-slate-50 rounded-lg mb-3">
                    <span class="font-mono text-2xl font-bold text-rose-600">+00:05:00</span>
                </div>
                <div class="text-right font-semibold text-pink-600">₱400</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-slate-900 mb-4">Recent Activity</h2>
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">Time</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">Room</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">Action</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm">14:23</td>
                            <td class="px-6 py-4 font-medium">Room 2</td>
                            <td class="px-6 py-4 text-sm">Order added</td>
                            <td class="px-6 py-4 text-right text-emerald-600 font-bold">+₱35</td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm">14:15</td>
                            <td class="px-6 py-4 font-medium">Room 1</td>
                            <td class="px-6 py-4 text-sm">Session extended</td>
                            <td class="px-6 py-4 text-right text-sky-600 font-bold">+30 min</td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm">13:58</td>
                            <td class="px-6 py-4 font-medium">Room 5</td>
                            <td class="px-6 py-4 text-sm">New session started</td>
                            <td class="px-6 py-4 text-right text-pink-600 font-bold">₱150</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
