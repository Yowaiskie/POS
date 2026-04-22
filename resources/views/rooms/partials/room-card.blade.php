@php
    $session = $room->activeSession;
    $status = $session?->status ?? 'available';
    $bill = $session ? $session->orders->sum('total_amount') : 0;
@endphp

<div class="rounded-xl border transition-all duration-200 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col p-6"
     style="box-shadow: var(--shadow)"
     x-data="{ 
        endTime: '{{ $session?->ends_at?->toIso8601String() }}',
        timer: '00:00:00',
        status: '{{ $status }}',
        isOvertime: false,
        updateTimer() {
            if (!this.endTime) return;
            const end = new Date(this.endTime);
            const now = new Date();
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
     x-init="if(endTime) { updateTimer(); setInterval(() => updateTimer(), 1000) }"
     :class="{
        'border-emerald-200 bg-emerald-50': status === 'active',
        'border-amber-200 bg-amber-50': status === 'warning',
        'border-rose-200 bg-red-50': status === 'overtime',
        'border-slate-200 bg-white': status === 'available'
     }">
    
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $room->name }}</h3>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border"
                  :class="{
                    'bg-emerald-100 text-emerald-700 border-emerald-200': status === 'active',
                    'bg-amber-100 text-amber-700 border-amber-200': status === 'warning',
                    'bg-red-100 text-red-700 border-red-200': status === 'overtime',
                    'bg-slate-100 text-slate-600 border-slate-200': status === 'available'
                  }">
                <span class="w-2 h-2 rounded-full transition-all duration-500" 
                      :class="{
                        'bg-emerald-500 animate-pulse': status === 'active',
                        'bg-amber-500 animate-pulse': status === 'warning',
                        'bg-red-500 animate-pulse': status === 'overtime',
                        'bg-slate-400': status === 'available'
                      }"></span>
                <span class="capitalize" x-text="status"></span>
            </span>
        </div>
        @if($status !== 'available')
        <div class="text-right bg-white px-4 py-2 rounded-lg border border-slate-200" style="box-shadow: var(--shadow-sm)">
            <div class="text-xs text-slate-500 font-medium">Current Bill</div>
            <div class="text-xl font-bold text-indigo-600">₱{{ number_format($bill) }}</div>
        </div>
        @endif
    </div>

    @if($status !== 'available')
    <div class="my-6 flex justify-center flex-1 items-center bg-white rounded-lg py-4" style="box-shadow: var(--shadow-sm)">
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
            <template x-if="isOvertime">
                <div class="text-sm font-semibold text-[--status-danger] mt-2 animate-pulse">OVERTIME</div>
            </template>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-2.5 mt-auto">
        @if($status === 'available')
            <button @click='openStartSessionModal(@json($room))' class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md hover:shadow-lg">
                Start Session
            </button>
        @else
            <button @click='openDetailModal(@json($room), @json($session))' 
                    class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md hover:shadow-lg">
                View Details
            </button>
            <div class="grid grid-cols-2 gap-2.5">
                <form action="{{ route('rooms.extend-session', $session) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:scale-98 transition-all font-semibold shadow-sm">
                        Extend
                    </button>
                </form>
                <button @click='openBillOutModal(@json($room), @json($session))' 
                        class="w-full px-4 py-2.5 bg-rose-500 text-white rounded-lg hover:bg-rose-600 active:scale-98 transition-all font-semibold shadow-sm">
                    End
                </button>
            </div>
        @endif
    </div>
</div>
