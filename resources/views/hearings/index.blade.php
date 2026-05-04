@extends('layouts.app')

@section('title', 'Calendar')
@section('page-title', 'Calendar')
@section('page-subtitle', 'Schedule and manage court hearings')

@section('topbar-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <a href="{{ route('hearings.create') }}" class="btn btn-gold btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New Hearing
        </a>
    @endif
@endsection

@section('content')

{{-- Month navigation --}}
<div class="calendar-nav">
    <a href="{{ route('hearings.index', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}"
       class="btn btn-outline btn-sm">← Prev</a>
    <h3>{{ $currentMonth->format('F Y') }}</h3>
    <a href="{{ route('hearings.index', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}"
       class="btn btn-outline btn-sm">Next →</a>
</div>

{{-- Calendar grid --}}
<div class="card" style="margin-bottom:1.2rem;">
    <div class="card-body" style="padding:0;">
        <div class="calendar-grid">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="cal-day-header">{{ $day }}</div>
            @endforeach

            {{-- Empty cells before first day --}}
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="cal-day empty"></div>
            @endfor

            {{-- Days of the month --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php $isToday = ($d == now()->day && $month == now()->month && $year == now()->year); @endphp
                <div class="cal-day {{ $isToday ? 'today' : '' }}">
                    <div class="cal-date">{{ $d }}</div>
                    @if(isset($calendarHearings[$d]))
                        @foreach($calendarHearings[$d] as $h)
                            <a href="{{ route('hearings.show', $h) }}" title="{{ $h->title }}">
                                <div class="cal-event">{{ $h->title }}</div>
                            </a>
                        @endforeach
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- Hearing list --}}
<div class="card">
    <div class="card-header">
        <h3>All Hearings – {{ $currentMonth->format('F Y') }}</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Hearing</th>
                    <th>Case</th>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hearings as $hearing)
                <tr>
                    <td style="font-weight:600; color:var(--navy);">{{ $hearing->title }}</td>
                    <td>
                        <a href="{{ route('cases.show', $hearing->legalCase) }}" style="color:var(--navy);">
                            {{ $hearing->legalCase->title ?? '–' }}
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($hearing->hearing_date)->format('M d, Y · h:i A') }}</td>
                    <td>{{ $hearing->location ?? '–' }}</td>
                    <td><span class="badge badge-{{ $hearing->status }}">{{ ucfirst($hearing->status) }}</span></td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('hearings.show', $hearing) }}" class="btn btn-outline btn-sm">View</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                                <a href="{{ route('hearings.edit', $hearing) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('hearings.destroy', $hearing) }}"
                                      onsubmit="return confirm('Delete this hearing?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Del</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--gray-400); padding:2rem;">
                        No hearings this month.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hearings->hasPages())
    <div class="pagination-wrap" style="padding:0.75rem 1rem;">
        {{ $hearings->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection