@extends('layouts.app')

@section('title', 'Edit Hearing')
@section('page-title', 'Edit Hearing')
@section('page-subtitle', 'Update hearing details')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Edit: {{ $hearing->title }}</h3>
        <a href="{{ route('hearings.show', $hearing) }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('hearings.update', $hearing) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label>Case *</label>
                <select name="case_id" class="form-control" required>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}"
                            {{ old('case_id', $hearing->case_id) == $case->id ? 'selected' : '' }}>
                            {{ $case->title }} (#{{ $case->case_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Hearing Title *</label>
                <input type="text" name="title" class="form-control"
                       value="{{ old('title', $hearing->title) }}" required>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Date & Time *</label>
                    <input type="datetime-local" name="hearing_date" class="form-control"
                           value="{{ old('hearing_date', \Carbon\Carbon::parse($hearing->hearing_date)->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['scheduled','completed','postponed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $hearing->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control"
                       value="{{ old('location', $hearing->location) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control">{{ old('notes', $hearing->notes) }}</textarea>
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Update Hearing</button>
                <a href="{{ route('hearings.show', $hearing) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection