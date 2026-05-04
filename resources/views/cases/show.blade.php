@extends('layouts.app')

@section('title', $case->title)
@section('page-title', $case->title)
@section('page-subtitle', 'Case #' . $case->case_number)

@section('topbar-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary btn-sm">Edit Case</a>
    @endif
    <a href="{{ route('cases.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')

{{-- Case Info --}}
<div class="card" style="margin-bottom:1.2rem;">
    <div class="card-header">
        <h3>Case Information</h3>
        <span class="badge badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item"><label>Case Number</label><p>#{{ $case->case_number }}</p></div>
            <div class="info-item"><label>Status</label><p><span class="badge badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></p></div>
            <div class="info-item"><label>Client</label><p>{{ $case->client->user->name ?? '–' }}</p></div>
            <div class="info-item"><label>Assigned Lawyer</label><p>{{ $case->lawyer->user->name ?? '–' }}</p></div>
            <div class="info-item"><label>Filed Date</label><p>{{ $case->filed_date ? $case->filed_date->format('F d, Y') : '–' }}</p></div>
            <div class="info-item"><label>Created</label><p>{{ $case->created_at->format('F d, Y') }}</p></div>
        </div>
        @if($case->description)
            <div style="margin-top:1.2rem; padding-top:1rem; border-top:1px solid var(--gray-200);">
                <label style="font-size:0.72rem; text-transform:uppercase; color:var(--gray-400); font-weight:600;">Description</label>
                <p style="margin-top:0.4rem; color:var(--gray-800); line-height:1.6;">{{ $case->description }}</p>
            </div>
        @endif
    </div>
</div>

<div class="two-col">
    {{-- Hearings --}}
    <div class="card">
        <div class="card-header">
            <h3>Hearings ({{ $case->hearings->count() }})</h3>
            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                <a href="{{ route('hearings.create') }}?case_id={{ $case->id }}" class="btn btn-gold btn-sm">+ Add</a>
            @endif
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($case->hearings->sortBy('hearing_date') as $hearing)
                    <tr>
                        <td>
                            <a href="{{ route('hearings.show', $hearing) }}" style="color:var(--navy); font-weight:600;">
                                {{ $hearing->title }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($hearing->hearing_date)->format('M d, Y h:i A') }}</td>
                        <td><span class="badge badge-{{ $hearing->status }}">{{ ucfirst($hearing->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; color:var(--gray-400); padding:1.5rem;">No hearings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Documents --}}
    <div class="card">
        <div class="card-header">
            <h3>Documents ({{ $case->documents->count() }})</h3>
            <a href="{{ route('documents.create') }}?case_id={{ $case->id }}" class="btn btn-gold btn-sm">+ Upload</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title</th><th>Type</th><th>Uploaded</th><th></th></tr></thead>
                <tbody>
                    @forelse($case->documents as $doc)
                    <tr>
                        <td style="font-weight:600; color:var(--navy);">{{ $doc->title }}</td>
                        <td style="text-transform:uppercase; font-size:0.78rem;">{{ $doc->file_type ?? '–' }}</td>
                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-outline btn-sm">⬇</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; color:var(--gray-400); padding:1.5rem;">No documents yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection