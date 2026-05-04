@if ($paginator->hasPages())
    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:1rem; font-size:0.82rem; color:var(--gray-600);">
        
        <span>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results</span>

        <div style="display:flex; gap:0.25rem; align-items:center;">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="display:inline-flex; align-items:center; justify-content:center;
                             width:32px; height:32px; border-radius:6px;
                             border:1.5px solid var(--gray-200); color:var(--gray-400);
                             background:var(--gray-100); cursor:not-allowed;">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   style="display:inline-flex; align-items:center; justify-content:center;
                          width:32px; height:32px; border-radius:6px;
                          border:1.5px solid var(--gray-200); color:var(--gray-800);
                          background:var(--white); text-decoration:none; transition:all 0.15s;"
                   onmouseover="this.style.background='#1a2757'; this.style.color='white';"
                   onmouseout="this.style.background='white'; this.style.color='#2c3252';">‹</a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display:inline-flex; align-items:center; justify-content:center;
                                 width:32px; height:32px; color:var(--gray-400);">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display:inline-flex; align-items:center; justify-content:center;
                                         width:32px; height:32px; border-radius:6px;
                                         background:#1a2757; color:white; font-weight:600;
                                         border:1.5px solid #1a2757;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               style="display:inline-flex; align-items:center; justify-content:center;
                                      width:32px; height:32px; border-radius:6px;
                                      border:1.5px solid var(--gray-200); color:var(--gray-800);
                                      background:var(--white); text-decoration:none; transition:all 0.15s;"
                               onmouseover="this.style.background='#1a2757'; this.style.color='white';"
                               onmouseout="this.style.background='white'; this.style.color='#2c3252';">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   style="display:inline-flex; align-items:center; justify-content:center;
                          width:32px; height:32px; border-radius:6px;
                          border:1.5px solid var(--gray-200); color:var(--gray-800);
                          background:var(--white); text-decoration:none; transition:all 0.15s;"
                   onmouseover="this.style.background='#1a2757'; this.style.color='white';"
                   onmouseout="this.style.background='white'; this.style.color='#2c3252';">›</a>
            @else
                <span style="display:inline-flex; align-items:center; justify-content:center;
                             width:32px; height:32px; border-radius:6px;
                             border:1.5px solid var(--gray-200); color:var(--gray-400);
                             background:var(--gray-100); cursor:not-allowed;">›</span>
            @endif

        </div>
    </div>
@endifb