@extends('layouts.app')

@section('title', 'Documents')
@section('page-title', 'Documents')
@section('page-subtitle', 'Manage case documents and files')

@section('topbar-actions')
    <a href="{{ route('documents.create') }}" class="btn btn-gold btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Upload Document
    </a>
@endsection

@section('content')

<form method="GET" class="filter-bar">
    <div class="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" class="form-control"
               placeholder="Search by document title..." value="{{ request('search') }}">
    </div>
    <select name="case_id" class="form-control" style="width:auto; min-width:180px;">
        <option value="">All Cases</option>
        @foreach($cases as $c)
            <option value="{{ $c->id }}" {{ request('case_id') == $c->id ? 'selected' : '' }}>
                {{ $c->title }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if(request()->hasAny(['search','case_id']))
        <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Case ID</th>
                    <th>Case</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Uploaded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.6rem;">
                            <div class="file-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                                    <polyline points="13 2 13 9 20 9"/>
                                </svg>
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--navy);">{{ $doc->title }}</div>
                                @if($doc->description)
                                    <div style="font-size:0.72rem; color:var(--gray-400);">{{ Str::limit($doc->description, 40) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace; font-size:0.8rem;">#{{ $doc->legalCase->case_number ?? '–' }}</td>
                    <td>{{ $doc->legalCase->title ?? '–' }}</td>
                    <td>{{ $doc->legalCase->client->user->name ?? '–' }}</td>
                    <td style="text-transform:uppercase; font-size:0.78rem; font-weight:600;">{{ $doc->file_type ?? '–' }}</td>
                    <td>{{ $doc->formatted_size }}</td>
                    <td>{{ $doc->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-outline btn-sm" title="Download">⬇</a>
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline btn-sm">View</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                                <a href="{{ route('documents.edit', $doc) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                      onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Del</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:var(--gray-400); padding:3rem;">
                        No documents found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
    <div class="pagination-wrap" style="padding:0.75rem 1rem;">
        <span>{{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }}</span>
        {{ $documents->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection