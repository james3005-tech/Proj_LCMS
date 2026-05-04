@extends('layouts.app')

@section('title', $hearing->title)
@section('page-title', $hearing->title)
@section('page-subtitle', 'Hearing details')

@section('topbar-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <a href="{{ route('hearings.edit', $hearing) }}" class="btn btn-primary btn-sm">Edit</a>
    @endif
    <a href="{{ route('hearings.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Hearing Information</h3>
        <span class="badge badge-{{ $hearing->status }}">{{ ucfirst($hearing->status) }}</span>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <label>Hearing Title</label>
                <p>{{ $hearing->title }}</p>
            </div>
            <div class="info-item">
                <label>Status</label>
                <p><span class="badge badge-{{ $hearing->status }}">{{ ucfirst($hearing->status) }}</span></p>
            </div>
            <div class="info-item">
                <label>Case</label>
                <p>
                    <a href="{{ route('cases.show', $hearing->legalCase) }}" style="color:var(--navy); font-weight:600;">
                        {{ $hearing->legalCase->title ?? '–' }}
                    </a>
                </p>
            </div>
            <div class="info-item">
                <label>Client</label>
                <p>{{ $hearing->legalCase->client->user->name ?? '–' }}</p>
            </div>
            <div class="info-item">
                <label>Lawyer</label>
                <p>{{ $hearing->legalCase->lawyer->user->name ?? '–' }}</p>
            </div>
            <div class="info-item">
                <label>Date & Time</label>
                <p>{{ \Carbon\Carbon::parse($hearing->hearing_date)->format('F d, Y · h:i A') }}</p>
            </div>
            <div class="info-item">
                <label>Location</label>
                <p>{{ $hearing->location ?? '–' }}</p>
            </div>
        </div>

        @if($hearing->notes)
            <div style="margin-top:1.2rem; padding-top:1rem; border-top:1px solid var(--gray-200);">
                <label style="font-size:0.72rem; text-transform:uppercase; color:var(--gray-400); font-weight:600;">Notes</label>
                <p style="margin-top:0.4rem; line-height:1.6;">{{ $hearing->notes }}</p>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--gray-200); display:flex; gap:0.7rem;">
            <a href="{{ route('hearings.edit', $hearing) }}" class="btn btn-primary btn-sm">Edit Hearing</a>
            <form method="POST" action="{{ route('hearings.destroy', $hearing) }}"
                  onsubmit="return confirm('Delete this hearing?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection