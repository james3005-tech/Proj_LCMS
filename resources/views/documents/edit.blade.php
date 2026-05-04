@extends('layouts.app')

@section('title', 'Edit Document')
@section('page-title', 'Edit Document')
@section('page-subtitle', 'Update document information')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3>Edit: {{ $document->title }}</h3>
        <a href="{{ route('documents.show', $document) }}" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label>Case *</label>
                <select name="case_id" class="form-control {{ $errors->has('case_id') ? 'is-invalid' : '' }}" required>
                    <option value="">Select a case...</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}"
                            {{ old('case_id', $document->case_id) == $case->id ? 'selected' : '' }}>
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
                       value="{{ old('title', $document->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"
                          placeholder="Brief description of this document...">{{ old('description', $document->description) }}</textarea>
            </div>

            <div class="form-group">
                <label>Replace File <span style="color:var(--gray-400); font-weight:400;">(optional)</span></label>

                {{-- Current file info --}}
                <div style="display:flex; align-items:center; gap:0.6rem; padding:0.7rem 0.9rem;
                            background:var(--gray-100); border-radius:var(--radius-sm); margin-bottom:0.6rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                        <polyline points="13 2 13 9 20 9"/>
                    </svg>
                    <span style="font-size:0.85rem; color:var(--gray-600);">
                        Current: {{ $document->title }}.{{ $document->file_type }}
                        ({{ $document->formatted_size }})
                    </span>
                </div>

                <input type="file" name="file"
                       class="form-control {{ $errors->has('file') ? 'is-invalid' : '' }}">
                <div style="font-size:0.78rem; color:var(--gray-400); margin-top:0.3rem;">
                    Leave empty to keep the current file. Max size: 20MB.
                </div>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; gap:0.7rem; margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Update Document</button>
                <a href="{{ route('documents.show', $document) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection