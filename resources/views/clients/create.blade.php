@extends('layouts.app')

@section('title', 'Add Client')
@section('page-title', 'Add New Client')
@section('page-subtitle', 'Create a new client account')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Client Information</h3>
        <a href="{{ route('clients.index') }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('clients.store') }}">
            @csrf

            <div class="two-col">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" required placeholder="Juan dela Cruz">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone') }}" placeholder="+63 912 345 6789">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" required placeholder="client@email.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       required placeholder="Min. 8 characters">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control"
                       value="{{ old('address') }}" placeholder="Street, Barangay, City, Province">
            </div>

            

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Save Client</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection