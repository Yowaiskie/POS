@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-6 px-4">
        <!-- Results Summary (Left) -->
        <div class="order-2 md:order-1">
            <p class="text-sm text-slate-500 font-medium bg-slate-50 px-4 py-2 rounded-full border border-slate-100 shadow-sm">
                Showing <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span> to <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span> 
                <span class="mx-1 text-slate-300">|</span> 
                Total of <span class="font-bold text-indigo-600">{{ $paginator->total() }}</span> entries
            </p>
        </div>

        <!-- Pagination Controls (Right) -->
        <nav role="navigation" aria-label="Pagination" class="order-1 md:order-2">
            <ul class="flex items-center gap-1 md:gap-2">
                {{-- Previous Page Link --}}
                <li>
                    @if ($paginator->onFirstPage())
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" 
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-600 border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 hover:shadow-md hover:-translate-y-0.5 transition-all active:scale-90 shadow-sm group">
                            <i data-lucide="chevron-left" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"></i>
                        </a>
                    @endif
                </li>

                {{-- Pagination Elements --}}
                <div class="flex items-center gap-1 md:gap-1.5 px-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li>
                                <span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold">...</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <li>
                                    @if ($page == $paginator->currentPage())
                                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white font-bold shadow-lg shadow-indigo-200 ring-2 ring-indigo-500 ring-offset-2">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-600 border border-slate-200 font-semibold hover:border-indigo-500 hover:text-indigo-600 hover:shadow-md hover:-translate-y-0.5 transition-all active:scale-90 shadow-sm">
                                            {{ $page }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                <li>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" 
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-600 border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 hover:shadow-md hover:-translate-y-0.5 transition-all active:scale-90 shadow-sm group">
                            <i data-lucide="chevron-right" class="w-5 h-5 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    @else
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
@endif
