@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')
@section('page-subtitle', 'Manage client information')

@section('topbar-actions')
    <a href="{{ route('clients.create') }}" class="btn btn-gold btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Client
    </a>
@endsection

@section('content')

{{-- Search --}}
<form method="GET" class="filter-bar">
    <div class="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" class="form-control"
               placeholder="Search by name or email..." value="{{ request('search') }}">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
    @if(request('search'))
        <a href="{{ route('clients.index') }}" class="btn btn-outline btn-sm">Clear</a>
    @endif
</form>


{{-- Client grid --}}
@if($clients->count())
<div class="client-grid">
    @foreach($clients as $client)
    <div class="client-card">
        <div class="client-avatar">{{ strtoupper(substr($client->user->name, 0, 1)) }}</div>
        <h4>{{ $client->user->name }}</h4>
        <div class="client-email">{{ $client->user->email }}</div>
        @if($client->user->phone)
            <div class="client-meta">📞 {{ $client->user->phone }}</div>
        @endif
        @if($client->address)
            <div class="client-meta" style="margin-top:0.2rem;">📍 {{ $client->address }}</div>
        @endif
        <div class="client-meta" style="margin-top:0.4rem;">
            <strong>{{ $client->cases()->count() }}</strong> case(s)
        </div>
        <div class="card-actions">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline btn-sm">View</a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm">Edit</a>
            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                  onsubmit="return confirm('Delete this client? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Del</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<div class="pagination-wrap" style="margin-top:1.2rem;">
    <span>Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }}</span>
    {{ $clients->appends(request()->query())->links() }}
</div>

@else
<div class="card">
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
        </svg>
        <h3>No clients found</h3>
        <p>Start by adding your first client.</p>
        <a href="{{ route('clients.create') }}" class="btn btn-primary" style="margin-top:1rem;">Add Client</a>
    </div>
</div>
@endif
@endsection