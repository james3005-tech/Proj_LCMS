@extends('layouts.app')

@section('title', 'New Hearing')
@section('page-title', 'Schedule Hearing')
@section('page-subtitle', 'Add a new court hearing')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Hearing Details</h3>
        <a href="{{ route('hearings.index') }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('hearings.store') }}">
            @csrf

            <div class="form-group">
                <label>Case *</label>
                <select name="case_id" class="form-control" required>
                    <option value="">Select case...</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}"
                            {{ (old('case_id') ?? request('case_id')) == $case->id ? 'selected' : '' }}>
                            {{ $case->title }} (#{{ $case->case_number }}) – {{ $case->client->user->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('case_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Hearing Title *</label>
                <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                       value="{{ old('title') }}" required placeholder="e.g. Preliminary Hearing, Arraignment...">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Date & Time *</label>
                    <input type="datetime-local" name="hearing_date" class="form-control"
                           value="{{ old('hearing_date') }}" required>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['scheduled','completed','postponed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', 'scheduled') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Location / Court Room</label>
                <input type="text" name="location" class="form-control"
                       value="{{ old('location') }}" placeholder="e.g. RTC Branch 1, Davao City Hall of Justice">
            </div>

           

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Schedule Hearing</button>
                <a href="{{ route('hearings.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection