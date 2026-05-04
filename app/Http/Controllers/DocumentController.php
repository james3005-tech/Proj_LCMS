<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Document::with(['legalCase.client.user', 'uploader']);

        if ($user->role === 'client' && $user->client) {
            $query->whereHas('legalCase', fn($q) => $q->where('client_id', $user->client->id));
        } elseif ($user->role === 'lawyer' && $user->lawyer) {
            $query->whereHas('legalCase', fn($q) => $q->where('lawyer_id', $user->lawyer->id));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title', 'like', "%$s%");
        }

        if ($request->filled('case_id')) {
            $query->where('case_id', $request->case_id);
        }

        $documents = $query->latest()->paginate(10);
        $cases     = LegalCase::all();
        return view('documents.index', compact('documents', 'cases'));
    }

    public function create()
    {
        $cases = LegalCase::with('client.user')->get();
        return view('documents.create', compact('cases'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'case_id'     => ['required', 'exists:cases,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file'        => ['required', 'file', 'max:20480'],
        ]);

        $file     = $request->file('file');
        $path     = $file->store('documents', 'public');
        $fileType = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();

        Document::create([
            'case_id'     => $data['case_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path'   => $path,
            'file_type'   => $fileType,
            'file_size'   => $fileSize,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load(['legalCase.client.user', 'legalCase.lawyer.user', 'uploader']);
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $cases = LegalCase::with('client.user')->get();
        return view('documents.edit', compact('document', 'cases'));
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'case_id'     => ['required', 'exists:cases,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file'        => ['nullable', 'file', 'max:20480'],
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $file             = $request->file('file');
            $data['file_path'] = $file->store('documents', 'public');
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        unset($data['file']);
        $document->update($data);
        return redirect()->route('documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);
        $fileName = $document->title . '.' . $document->file_type;
        return response()->download($filePath, $fileName);
    }
}