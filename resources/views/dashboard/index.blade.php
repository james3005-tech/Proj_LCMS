@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Here\'s your case overview for today')

@section('topbar-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <a href="{{ route('cases.create') }}" class="btn btn-gold btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New Case
        </a>
        <a href="{{ route('clients.create') }}" class="btn btn-outline btn-sm">+ New Client</a>
    @endif
@endsection

@section('content')

{{-- ── Stats ──────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon navy">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Clients</div>
            <div class="stat-value">{{ $totalClients }}</div>
        </div>
    </div>

    <div class="stat-card">
        
        <div class="stat-icon gold">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Active Cases</div>
            <div class="stat-value">{{ $activeCases }}</div>

        </div>
        
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Hearings Today</div>
            <div class="stat-value">{{ $todayHearings }}</div>

        </div>

        
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                <polyline points="13 2 13 9 20 9"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Documents</div>
            <div class="stat-value">{{ $totalDocuments }}</div>
        </div>
    </div>

    <div class="stat-card">
        
        <div class="stat-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pending Cases</div>
            <div class="stat-value">{{ $casesByStatus['pending'] }}</div>

        </div>
        
    </div>

    <div class="stat-card">
        <div class="stat-icon navy">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </div>
         <div class="stat-info">
            <div class="stat-label">Total Cases</div>
            <div class="stat-value">{{ $totalCases }}</div>

        </div>
        
    </div>
</div>


{{-- ── Case Status Breakdown ───────────────────────────────── --}}
<div class="card" style="margin-bottom:1.2rem;">
    <div class="card-header">
        <h3>Case Status Overview</h3>
    </div>
    <div class="card-body">
        <div class="stats-grid" style="margin-bottom:0;">
            @foreach($casesByStatus as $status => $count)
            <div style="text-align:center; padding:0.8rem; background:var(--gray-100); border-radius:var(--radius-sm);">
                <div style="font-size:1.4rem; font-weight:700; color:var(--navy);">{{ $count }}</div>
                <span class="badge badge-{{ $status }}" style="margin-top:0.3rem;">{{ ucfirst($status) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>


{{-- ── Dashboard Grid ──────────────────────────────────────── --}}
<div class="dashboard-grid">

{{-- Pending Requests Notice for Lawyers --}}
@if(auth()->user()->isLawyer())
    @php
        $pendingCount = \App\Models\LegalCase::where('status','pending')
            ->where('lawyer_id', auth()->user()->lawyer->id ?? 0)
            ->count();
    @endphp
    @if($pendingCount > 0)
    <div class="alert" style="background:rgba(232,160,32,0.1); border-left:4px solid var(--gold);
                              color:var(--gray-800); margin-bottom:1.2rem;">
        ⏳ You have <strong>{{ $pendingCount }} pending case request(s)</strong> waiting for your review.
        <a href="{{ route('cases.index') }}" style="color:var(--navy); font-weight:600; margin-left:0.5rem;">
            Review Now →
        </a>
    </div>
    @endif
@endif

{{-- Pending Status Notice for Clients --}}
@if(auth()->user()->isClient())
    @php
        $myPending = \App\Models\LegalCase::where('status','pending')
            ->where('client_id', auth()->user()->client->id ?? 0)
            ->count();
        $myDenied  = \App\Models\LegalCase::where('status','dismissed')
            ->where('client_id', auth()->user()->client->id ?? 0)
            ->whereNotNull('denial_reason')
            ->count();
    @endphp
    @if($myPending > 0)
    <div class="alert" style="background:rgba(59,130,246,0.08); border-left:4px solid var(--info);
                              color:var(--gray-800); margin-bottom:1rem;">
        ⏳ You have <strong>{{ $myPending }} case request(s)</strong> waiting for lawyer review.
    </div>
    @endif
    @if($myDenied > 0)
    <div class="alert alert-danger" style="margin-bottom:1rem;">
        ✗ You have <strong>{{ $myDenied }} case request(s)</strong> that were denied.
        <a href="{{ route('cases.index', ['status' => 'dismissed']) }}"
           style="color:var(--danger); font-weight:600; margin-left:0.5rem;">
            View Details →
        </a>
    </div>
    @endif
@endif

    {{-- Recent Cases --}}
    <div class="card">
        <div class="card-header">
            <h3>Recent Cases</h3>
            <a href="{{ route('cases.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Case</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Filed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCases as $case)
                    <tr>
                        <td>
                            <a href="{{ route('cases.show', $case) }}"
                               style="font-weight:600; color:var(--navy);">
                                {{ $case->title }}
                            </a>
                            <div style="font-size:0.75rem; color:var(--gray-400);">#{{ $case->case_number }}</div>
                        </td>
                        <td>{{ $case->client->user->name ?? '–' }}</td>
                        <td><span class="badge badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
                        <td>{{ $case->filed_date ? $case->filed_date->format('M d, Y') : '–' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--gray-400); padding:2rem;">No cases found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    
    {{-- Upcoming Hearings --}}
    <div class="card">
        <div class="card-header">
            <h3>Upcoming Hearings</h3>
            <a href="{{ route('hearings.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="card-body">
            @forelse($upcomingHearings as $hearing)
            <div class="hearing-item">
                <div class="hearing-date-badge">
                    <div class="hd-day">{{ \Carbon\Carbon::parse($hearing->hearing_date)->format('d') }}</div>
                    <div class="hd-mon">{{ \Carbon\Carbon::parse($hearing->hearing_date)->format('M') }}</div>
                </div>
                <div class="hearing-info">
                    <h4>{{ $hearing->title }}</h4>
                    <p>{{ $hearing->legalCase->title ?? '–' }} · {{ \Carbon\Carbon::parse($hearing->hearing_date)->format('h:i A') }}</p>
                    @if($hearing->location)
                        <p style="color:var(--gray-600);">📍 {{ $hearing->location }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:1.5rem;">
                <p>No upcoming hearings in the next 7 days.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection