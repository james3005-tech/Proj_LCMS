@extends('layouts.app')

@section('title', $document->title)
@section('page-title', $document->title)
@section('page-subtitle', 'Document details')

@section('topbar-actions')
    <a href="{{ route('documents.download', $document) }}" class="btn btn-gold btn-sm">
        ⬇ Download
    </a>
    @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary btn-sm">Edit</a>
    @endif
    <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Document Information</h3>
        <span style="font-size:0.78rem; text-transform:uppercase; font-weight:700;
                     background:var(--gray-100); color:var(--gray-600);
                     padding:0.2rem 0.6rem; border-radius:4px;">
            {{ $document->file_type ?? 'FILE' }}
        </span>
    </div>
    <div class="card-body">

        {{-- File icon display --}}
        <div style="display:flex; align-items:center; gap:1rem; padding:1rem;
                    background:var(--gray-100); border-radius:var(--radius); margin-bottom:1.4rem;">
            <div class="file-icon" style="width:52px; height:52px; border-radius:12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                    <polyline points="13 2 13 9 20 9"/>
                </svg>
            </div>
            <div>
                <div style="font-weight:700; font-size:1rem; color:var(--navy);">{{ $document->title }}</div>
                <div style="font-size:0.78rem; color:var(--gray-400); margin-top:0.2rem;">
                    {{ $document->formatted_size }}
                    @if($document->file_type) · {{ strtoupper($document->file_type) }} @endif
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Case</label>
                <p>
                    <a href="{{ route('cases.show', $document->legalCase) }}"
                       style="color:var(--navy); font-weight:600;">
                        {{ $document->legalCase->title ?? '–' }}
                    </a>
                </p>
            </div>
            <div class="info-item">
                <label>Case Number</label>
                <p>#{{ $document->legalCase->case_number ?? '–' }}</p>
            </div>
            <div class="info-item">
                <label>Client</label>
                <p>{{ $document->legalCase->client->user->name ?? '–' }}</p>
            </div>
            <div class="info-item">
                <label>Uploaded By</label>
                <p>{{ $document->uploader->name ?? '–' }}</p>
            </div>
            <div class="info-item">
                <label>Date Uploaded</label>
                <p>{{ $document->created_at->format('F d, Y · h:i A') }}</p>
            </div>
            <div class="info-item">
                <label>File Size</label>
                <p>{{ $document->formatted_size }}</p>
            </div>
        </div>

        @if($document->description)
            <div style="margin-top:1.2rem; padding-top:1rem; border-top:1px solid var(--gray-200);">
                <label style="font-size:0.72rem; text-transform:uppercase; color:var(--gray-400); font-weight:600;">
                    Description
                </label>
                <p style="margin-top:0.4rem; line-height:1.6; color:var(--gray-800);">
                    {{ $document->description }}
                </p>
            </div>
        @endif

        <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--gray-200);
                    display:flex; gap:0.7rem; flex-wrap:wrap;">
            <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                ⬇ Download File
            </a>
            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline">Edit</a>
                <form method="POST" action="{{ route('documents.destroy', $document) }}"
                      onsubmit="return confirm('Delete this document? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">Delete</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection