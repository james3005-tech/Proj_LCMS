@extends('layouts.app')

@section('title', 'Edit Client')
@section('page-title', 'Edit Client')
@section('page-subtitle', 'Update client information')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Edit: {{ $client->user->name }}</h3>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf @method('PUT')

            <div class="two-col">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $client->user->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone', $client->user->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $client->user->email) }}" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control"
                       value="{{ old('address', $client->address) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Update Client</button>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
