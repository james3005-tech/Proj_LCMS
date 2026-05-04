@extends('layouts.app')

@section('title', 'Upload Document')
@section('page-title', 'Upload Document')
@section('page-subtitle', 'Add a new document to a case')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Document Details</h3>
        <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Case *</label>
                <select name="case_id" class="form-control {{ $errors->has('case_id') ? 'is-invalid' : '' }}" required>
                    <option value="">Select a case...</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}"
                            {{ (old('case_id') ?? request('case_id')) == $case->id ? 'selected' : '' }}>
                            {{ $case->title }} (#{{ $case->case_number }})
                            — {{ $case->client->user->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('case_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Document Title *</label>
                <input type="text" name="title"
                       class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                       value="{{ old('title') }}" required
                       placeholder="e.g. Complaint Filing, Witness Statement...">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"
                          placeholder="Brief description of this document...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>File *</label>
                <input type="file" name="file"
                       class="form-control {{ $errors->has('file') ? 'is-invalid' : '' }}"
                       required>
                <div style="font-size:0.78rem; color:var(--gray-400); margin-top:0.3rem;">
                    Accepted: PDF, DOC, DOCX, JPG, PNG. Max size: 20MB.
                </div>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Upload Document</button>
                <a href="{{ route('documents.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection