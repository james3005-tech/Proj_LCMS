@extends('layouts.app')

@section('title', 'Request a Case')
@section('page-title', 'Request a Case')
@section('page-subtitle', 'Submit a new case request to a lawyer')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Case Request Form</h3>
        <a href="{{ route('cases.index') }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div style="background:rgba(232,160,32,0.08); border-left:4px solid var(--gold);
                    padding:0.9rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.4rem;">
            <p style="font-size:0.88rem; color:var(--gray-800);">
                📋 Fill out the form below to submit a case request. The lawyer you select
                will review your request and either <strong>accept</strong> or
                <strong>deny</strong> it. You will be able to track the status
                from your Cases page.
            </p>
        </div>

        <form method="POST" action="{{ route('cases.request.submit') }}">
            @csrf

            <div class="form-group">
                <label>Case Title *</label>
                <input type="text" name="title"
                       class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                       value="{{ old('title') }}" required
                       placeholder="e.g. Property Dispute, Labor Case, etc.">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Select Lawyer *</label>
                <select name="lawyer_id"
                        class="form-control {{ $errors->has('lawyer_id') ? 'is-invalid' : '' }}"
                        required>
                    <option value="">Choose a lawyer...</option>
                    @foreach($lawyers as $lawyer)
                        <option value="{{ $lawyer->id }}"
                            {{ old('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
                            {{ $lawyer->user->name }}
                            @if($lawyer->specialization)
                                — {{ $lawyer->specialization }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('lawyer_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Case Description *</label>
                <textarea name="description"
                          class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                          rows="6" required
                          placeholder="Please describe your case in detail. Include relevant dates, parties involved, and what legal help you need...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <a href="{{ route('cases.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection