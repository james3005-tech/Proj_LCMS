@extends('layouts.app')

@section('title', 'New Case')
@section('page-title', 'New Case')
@section('page-subtitle', 'Create a new legal case')

@section('content')
<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>Case Details</h3>
        <a href="{{ route('cases.index') }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('cases.store') }}">
            @csrf

            <div class="two-col">
                <div class="form-group">
                    <label>Case Title *</label>
                    <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           value="{{ old('title') }}" required placeholder="e.g. People vs. Santos">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Case Number *</label>
                    <input type="text" name="case_number" class="form-control {{ $errors->has('case_number') ? 'is-invalid' : '' }}"
                           value="{{ old('case_number') }}" required placeholder="e.g. 2024-0001">
                    @error('case_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Client *</label>
                    <select name="client_id" class="form-control" required>
                        <option value="">Select client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Assigned Lawyer *</label>
                    <select name="lawyer_id" class="form-control" required>
                        <option value="">Select lawyer...</option>
                        @foreach($lawyers as $lawyer)
                            <option value="{{ $lawyer->id }}" {{ old('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
                                {{ $lawyer->user->name }}
                                @if($lawyer->specialization) ({{ $lawyer->specialization }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('lawyer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['active','pending','closed','dismissed'] as $s)
                            <option value="{{ $s }}" {{ old('status', 'pending') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Filed Date</label>
                    <input type="date" name="filed_date" class="form-control"
                           value="{{ old('filed_date') }}">
                </div>
            </div>

           

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Create Case</button>
                <a href="{{ route('cases.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection