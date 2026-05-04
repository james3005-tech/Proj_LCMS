@extends('layouts.app')

@section('title', 'Edit Case')
@section('page-title', 'Edit Case')
@section('page-subtitle', 'Update case information')

@section('content')
<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>Edit: {{ $case->title }}</h3>
        <a href="{{ route('cases.show', $case) }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('cases.update', $case) }}">
            @csrf @method('PUT')

            <div class="two-col">
                <div class="form-group">
                    <label>Case Title *</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', $case->title) }}" required>
                </div>
                <div class="form-group">
                    <label>Case Number *</label>
                    <input type="text" name="case_number" class="form-control"
                           value="{{ old('case_number', $case->case_number) }}" required>
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Client *</label>
                    <select name="client_id" class="form-control" required>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id', $case->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Assigned Lawyer *</label>
                    <select name="lawyer_id" class="form-control" required>
                        @foreach($lawyers as $lawyer)
                            <option value="{{ $lawyer->id }}"
                                {{ old('lawyer_id', $case->lawyer_id) == $lawyer->id ? 'selected' : '' }}>
                                {{ $lawyer->user->name }}
                                @if($lawyer->specialization) ({{ $lawyer->specialization }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['active','pending','closed','dismissed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $case->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Filed Date</label>
                    <input type="date" name="filed_date" class="form-control"
                           value="{{ old('filed_date', $case->filed_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $case->description) }}</textarea>
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Update Case</button>
                <a href="{{ route('cases.show', $case) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection