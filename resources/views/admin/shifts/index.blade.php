@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-8 max-w-7xl mx-auto" x-data="shiftManagement()">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i data-lucide="wallet" class="w-8 h-8 text-indigo-600"></i>
                Shift Management
            </h1>
            <p class="text-sm text-gray-500 mt-1">Monitor staff cash register shifts and discrepancies.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Shift ID</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Cashier</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Variance</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono text-sm text-gray-500">
                                #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900">{{ $shift->user->name }}</div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">
                                <div><span class="font-medium text-gray-900">Start:</span> {{ $shift->start_time->format('M d, h:i A') }}</div>
                                <div><span class="font-medium text-gray-900">End:</span> {{ $shift->end_time ? $shift->end_time->format('M d, h:i A') : 'Ongoing' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($shift->status === 'open')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Open
                                    </span>
                                @elseif($shift->status === 'closed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700">
                                        <i data-lucide="check" class="w-3 h-3"></i> Closed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-700">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i> Force Closed
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-medium">
                                @if($shift->status === 'open')
                                    <span class="text-gray-400">---</span>
                                @else
                                    @if($shift->difference_type === 'matched')
                                        <span class="text-emerald-600 font-bold">Match</span>
                                    @elseif($shift->difference_type === 'over')
                                        <span class="text-blue-600 font-bold">+₱{{ number_format($shift->difference_amount, 2) }}</span>
                                    @else
                                        <span class="text-red-600 font-bold">-₱{{ number_format(abs($shift->difference_amount), 2) }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    @if($shift->status === 'open')
                                        <button @click="openForceCloseModal({{ $shift->id }}, '{{ $shift->user->name }}')" 
                                                class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5 border border-red-200">
                                            <i data-lucide="power-off" class="w-3.5 h-3.5"></i> Force Close
                                        </button>
                                    @else
                                        <a href="{{ route('shifts.report', $shift->id) }}" 
                                           class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5 border border-indigo-200">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i> View Report
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <i data-lucide="wallet" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                                <div class="font-medium">No shifts recorded yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    {{-- Force Close Modal --}}
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div x-show="showModal"
             @click.away="closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-red-100">
            
            <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-red-50 to-white relative">
                <button @click="closeModal()" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-full transition-colors shadow-sm">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-4 shadow-sm border border-red-200">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Force Close Shift</h2>
                <p class="text-sm text-gray-500">You are about to forcibly close <span class="font-bold text-gray-900" x-text="selectedUser"></span>'s active shift.</p>
            </div>

            <form :action="'{{ url('shifts') }}/' + selectedShiftId + '/force-close'" method="POST" class="p-6 flex flex-col gap-4">
                @csrf
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800 flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 shrink-0 text-amber-600"></i>
                    <div>
                        <span class="font-bold">Warning:</span> Force closing will bypass the blind drop. The actual cash will automatically be set to the expected amount (Match = ₱0.00 difference).
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Notes (Reason)</label>
                    <textarea name="notes" required rows="3"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm text-gray-900 shadow-inner resize-none"
                              placeholder="e.g. Cashier forgot to log out before leaving."></textarea>
                </div>

                <div class="flex gap-3 pt-2 mt-2">
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded-xl transition-all shadow-sm">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-white bg-red-600 hover:bg-red-700 font-semibold rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                        <i data-lucide="power-off" class="w-4 h-4"></i> Confirm Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('shiftManagement', () => ({
            showModal: false,
            selectedShiftId: null,
            selectedUser: '',

            openForceCloseModal(shiftId, userName) {
                this.selectedShiftId = shiftId;
                this.selectedUser = userName;
                this.showModal = true;
                setTimeout(() => lucide.createIcons(), 10);
            },

            closeModal() {
                this.showModal = false;
                this.selectedShiftId = null;
                this.selectedUser = '';
            }
        }))
    });
</script>
@endsection
