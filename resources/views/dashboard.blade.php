@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Dashboard Overview</h1>
        <p class="text-slate-600">Real-time monitoring and analytics for your KTV operations</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10">
        <!-- Active Rooms -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1" style="box-shadow: var(--shadow-md)">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Active Rooms</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold" style="color: var(--status-active)">{{ $activeRoomsCount }}</div>
        </div>

        <!-- Available Rooms -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1" style="box-shadow: var(--shadow-md)">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Available Rooms</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold" style="color: var(--neon-blue)">{{ $availableRoomsCount }}</div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1" style="box-shadow: var(--shadow-md)">
            <div class="flex items-start justify-between mb-4">
                <div class="text-sm font-medium text-slate-600">Total Sales Today</div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center shadow-lg">
                    <i data-lucide="philippine-peso" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="text-4xl md:text-5xl font-bold" style="color: var(--neon-pink)">₱{{ number_format($totalSalesToday) }}</div>
        </div>
    </div>

    <!-- Active Sessions Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h2 class="text-xl md:text-2xl">Active Sessions</h2>
            <a href="{{ route('rooms.index') }}" class="px-6 py-2.5 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md w-full sm:w-auto font-medium text-center">
                View All Rooms
            </a>
        </div>

        @if(count($activeSessions) === 0)
            <div class="bg-[--card] border border-[--border] rounded-xl p-8 text-center text-[--muted-foreground] shadow-sm">
                No active sessions
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeSessions as $session)
                    @php
                        $status = $session['status'];
                    @endphp
                    <div class="bg-white border border-[--border] border-l-4 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer active:scale-95"
                         onclick="window.location='{{ route('rooms.index') }}'"
                         x-data="{ 
                            startTime: '{{ $session['started_at'] }}',
                            endTime: '{{ $session['ends_at'] }}',
                            timer: '00:00:00',
                            status: '{{ $status }}',
                            isOvertime: false,
                            updateTimer() {
                                const now = new Date();
                                if (!this.endTime) {
                                    const start = new Date(this.startTime);
                                    const diff = Math.floor((now - start) / 1000);
                                    const h = Math.floor(diff / 3600);
                                    const m = Math.floor((diff % 3600) / 60);
                                    const s = diff % 60;
                                    this.timer = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
                                    this.status = 'active';
                                    return;
                                }

                                const end = new Date(this.endTime);
                                const diff = end - now;
                                this.isOvertime = diff < 0;
                                const absDiff = Math.abs(diff) / 1000;
                                const h = Math.floor(absDiff / 3600);
                                const m = Math.floor((absDiff % 3600) / 60);
                                const s = Math.floor(absDiff % 60);
                                this.timer = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
                                if (this.isOvertime) this.status = 'overtime';
                                else if (diff <= 600000) this.status = 'warning';
                                else this.status = 'active';
                            }
                         }"
                         x-init="updateTimer(); setInterval(() => updateTimer(), 1000)"
                         :class="{
                            'border-l-[--status-active] bg-green-50': status === 'active',
                            'border-l-[--status-warning] bg-amber-50': status === 'warning',
                            'border-l-[--status-danger] bg-red-50': status === 'overtime'
                         }">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-semibold">{{ $session['room'] }}</h3>
                            <div class="text-xs text-[--muted-foreground] capitalize px-2 py-1 bg-white rounded" x-text="status"></div>
                        </div>
                        <div class="flex items-center justify-center py-2">
                            <div class="flex flex-col items-center">
                                <div class="font-mono text-xl md:text-2xl tracking-wider font-bold"
                                     :class="{
                                        'text-[--status-danger]': status === 'overtime',
                                        'text-[--status-warning]': status === 'warning',
                                        'text-[--status-active]': status === 'active'
                                     }"
                                     :style="{
                                        textShadow: status === 'overtime' 
                                            ? '0 0 20px var(--status-danger)' 
                                            : status === 'warning' 
                                            ? '0 0 15px var(--status-warning)' 
                                            : '0 0 10px var(--status-active)'
                                     }">
                                    <template x-if="isOvertime"><span class="text-lg mr-1">+</span></template>
                                    <span x-text="timer"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-3 font-semibold text-[--neon-pink]">
                            ₱{{ number_format($session['bill']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Activity Table -->
    <div>
        <h2 class="text-xl md:text-2xl mb-4">Recent Activity</h2>
        <div class="bg-[--card] border border-[--border] rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[--muted] border-b border-[--border]">
                        <tr>
                            <th class="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">Time</th>
                            <th class="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">Room</th>
                            <th class="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">Action</th>
                            <th class="text-right px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-[--border]">
                        @forelse($recentActivities as $activity)
                            <tr class="border-b border-[--border] hover:bg-[--muted]/50 transition-colors">
                                <td class="px-4 md:px-6 py-4 text-sm">{{ $activity['time'] }}</td>
                                <td class="px-4 md:px-6 py-4">{{ $activity['room'] }}</td>
                                <td class="px-4 md:px-6 py-4 text-sm">{{ $activity['action'] }}</td>
                                <td class="px-4 md:px-6 py-4 text-right font-semibold {{ $activity['amount_class'] }}">{{ $activity['amount_label'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-400 text-center" colspan="4">No recent activity.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
