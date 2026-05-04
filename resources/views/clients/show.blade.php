@extends('layouts.app')

@section('title', $client->user->name)
@section('page-title', $client->user->name)
@section('page-subtitle', 'Client profile and case history')

@section('topbar-actions')
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm">Edit Client</a>
    <a href="{{ route('clients.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@section('content')

<div class="two-col" style="margin-bottom:1.2rem;">

    {{-- Profile card --}}
    <div class="card">
        <div class="card-header"><h3>Profile</h3></div>
        <div class="card-body" style="text-align:center;">
            <div class="client-avatar" style="width:72px;height:72px;font-size:1.8rem;margin:0 auto 1rem;">
                {{ strtoupper(substr($client->user->name, 0, 1)) }}
            </div>
            <h3 style="font-family:var(--font-head); color:var(--navy);">{{ $client->user->name }}</h3>
            <p style="color:var(--gray-400); font-size:0.85rem; margin-top:0.25rem;">{{ $client->user->email }}</p>
            <span class="badge badge-client" style="margin-top:0.5rem;">Client</span>
        </div>
        <div class="card-body" style="border-top:1px solid var(--gray-200);">
            <div class="info-grid">
                <div class="info-item">
                    <label>Phone</label>
                    <p>{{ $client->user->phone ?? '–' }}</p>
                </div>
                <div class="info-item">
                    <label>Address</label>
                    <p>{{ $client->address ?? '–' }}</p>
                </div>
                <div class="info-item">
                    <label>Member Since</label>
                    <p>{{ $client->created_at->format('M d, Y') }}</p>
                </div>
                <div class="info-item">
                    <label>Total Cases</label>
                    <p>{{ $client->cases->count() }}</p>
                </div>
            </div>
            @if($client->notes)
            <div style="margin-top:1rem;">
                <label style="font-size:0.72rem; text-transform:uppercase; color:var(--gray-400); font-weight:600;">Notes</label>
                <p style="margin-top:0.3rem; font-size:0.88rem; color:var(--gray-800);">{{ $client->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Cases --}}
    <div class="card">
        <div class="card-header">
            <h3>Cases ({{ $client->cases->count() }})</h3>
            @if(auth()->user()->isAdmin() || auth()->user()->isLawyer())
                <a href="{{ route('cases.create') }}" class="btn btn-gold btn-sm">+ New Case</a>
            @endif
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Case Title</th>
                        <th>Status</th>
                        <th>Lawyer</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->cases as $case)
                    <tr>
                        <td>
                            <a href="{{ route('cases.show', $case) }}" style="font-weight:600; color:var(--navy);">
                                {{ $case->title }}
                            </a>
                            <div style="font-size:0.72rem; color:var(--gray-400);">#{{ $case->case_number }}</div>
                        </td>
                        <td><span class="badge badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
                        <td>{{ $case->lawyer->user->name ?? '–' }}</td>
                        <td><a href="{{ route('cases.show', $case) }}" class="btn btn-outline btn-sm">View</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--gray-400); padding:1.5rem;">
                            No cases yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection