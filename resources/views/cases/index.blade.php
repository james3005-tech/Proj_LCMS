@extends('layouts.app')

@section('title', 'Cases')
@section('page-title', 'Cases')
@section('page-subtitle', 'Manage all legal cases')

@section('topbar-actions')
    @if(auth()->user()->isClient())
        <a href="{{ route('cases.request.form') }}" class="btn btn-gold btn-sm">
            + Request a Case
        </a>
    @else
        <a href="{{ route('cases.create') }}" class="btn btn-gold btn-sm">
            + New Case
        </a>
    @endif
@endsection

@section('content')

{{-- Pending Requests for Lawyer/Admin --}}
@if(auth()->user()->isLawyer() || auth()->user()->isAdmin())
    @php
        $pendingCases = \App\Models\LegalCase::with(['client.user'])
            ->where('status', 'pending')
            ->when(auth()->user()->isLawyer(), fn($q) => $q->where('lawyer_id', auth()->user()->lawyer->id ?? 0))
            ->latest()->get();
    @endphp

    @if($pendingCases->count())
    <div class="card" style="margin-bottom:1.2rem; border-left:4px solid var(--gold);">
        <div class="card-header">
            <h3 style="color:var(--gold);">
                ⏳ Pending Case Requests ({{ $pendingCases->count() }})
            </h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Case Title</th>
                        <th>Client</th>
                        <th>Submitted</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingCases as $pending)
                    <tr>
                        <td style="font-weight:600; color:var(--navy);">
                            {{ $pending->title }}
                        </td>
                        <td>{{ $pending->client->user->name ?? '–' }}</td>
                        <td>{{ $pending->created_at->format('M d, Y') }}</td>
                        <td style="max-width:200px;">
                            {{ \Illuminate\Support\Str::limit($pending->description, 60) }}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                <a href="{{ route('cases.show', $pending) }}"
                                   class="btn btn-outline btn-sm">View</a>

                                {{-- Accept --}}
                                <form method="POST" action="{{ route('cases.accept', $pending) }}">
                                    @csrf
                                    <button class="btn btn-sm"
                                            style="background:var(--success); color:white;"
                                            onclick="return confirm('Accept this case?')">
                                        ✓ Accept
                                    </button>
                                </form>

                                {{-- Deny --}}
                                <button class="btn btn-danger btn-sm"
                                        onclick="openDenyModal({{ $pending->id }})">
                                    ✗ Deny
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endif

{{-- Search & Filter --}}
<form method="GET" class="filter-bar">
    <div class="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" class="form-control"
               placeholder="Search by title or case #..."
               value="{{ request('search') }}">
    </div>
    <select name="status" class="form-control" style="width:auto; min-width:140px;">
        <option value="">All Statuses</option>
        @foreach(['active','pending','closed','dismissed'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                {{ ucfirst($s) }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('cases.index') }}" class="btn btn-outline btn-sm">Clear</a>
    @endif
</form>

{{-- Cases Table --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Case Title</th>
                    <th>Case ID</th>
                    <th>Status</th>
                    <th>Client</th>
                    <th>Lawyer</th>
                    <th>Filed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                <tr>
                    <td>
                        <a href="{{ route('cases.show', $case) }}"
                           style="font-weight:600; color:var(--navy);">
                            {{ $case->title }}
                        </a>
                        @if($case->status === 'dismissed' && $case->denial_reason)
                            <div style="font-size:0.72rem; color:var(--danger); margin-top:0.2rem;">
                                ✗ Denied: {{ \Illuminate\Support\Str::limit($case->denial_reason, 40) }}
                            </div>
                        @endif
                    </td>
                    <td style="font-family:monospace; font-size:0.82rem;">
                        #{{ $case->case_number }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $case->status }}">
                            {{ ucfirst($case->status) }}
                        </span>
                    </td>
                    <td>{{ $case->client->user->name ?? '–' }}</td>
                    <td>{{ $case->lawyer->user->name ?? '–' }}</td>
                    <td>{{ $case->filed_date ? $case->filed_date->format('M d, Y') : '–' }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('cases.show', $case) }}"
                               class="btn btn-outline btn-sm">View</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                                <a href="{{ route('cases.edit', $case) }}"
                                   class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('cases.destroy', $case) }}"
                                      onsubmit="return confirm('Delete this case?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Del</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7"
                        style="text-align:center; color:var(--gray-400); padding:3rem;">
                        No cases found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cases->hasPages())
    <div class="pagination-wrap" style="padding:0.75rem 1rem;">
        <span>{{ $cases->firstItem() }}–{{ $cases->lastItem() }}
            of {{ $cases->total() }}</span>
        {{ $cases->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Deny Modal --}}
<div id="denyModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
     z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:var(--radius); padding:1.5rem;
                width:100%; max-width:460px; margin:1rem;">
        <h3 style="font-family:var(--font-head); color:var(--navy); margin-bottom:1rem;">
            Deny Case Request
        </h3>
        <form method="POST" id="denyForm">
            @csrf
            <div class="form-group">
                <label>Reason for Denial *</label>
                <textarea name="denial_reason" class="form-control" rows="4" required
                          placeholder="Please provide a reason for denying this case request..."></textarea>
            </div>
            <div style="display:flex; gap:0.7rem; margin-top:1rem;">
                <button type="submit" class="btn btn-danger">Confirm Deny</button>
                <button type="button" class="btn btn-outline"
                        onclick="closeDenyModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openDenyModal(caseId) {
    const modal = document.getElementById('denyModal');
    const form  = document.getElementById('denyForm');
    form.action = '/cases/' + caseId + '/deny';
    modal.style.display = 'flex';
}

function closeDenyModal() {
    document.getElementById('denyModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('denyModal').addEventListener('click', function(e) {
    if (e.target === this) closeDenyModal();
});
</script>
@endpush

@endsection