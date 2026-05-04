<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lawyer;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = LegalCase::with(['client.user', 'lawyer.user']);

        if ($user->isClient() && $user->client) {
            $query->where('client_id', $user->client->id);
        } elseif ($user->isLawyer() && $user->lawyer) {
            $query->where('lawyer_id', $user->lawyer->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%$s%")
                ->orWhere('case_number', 'like', "%$s%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cases = $query->latest()->paginate(5);
        return view('cases.index', compact('cases'));
    }

    public function create()
    {
        $user = Auth::user();

        // Client uses request form instead
        if ($user->isClient()) {
            return redirect()->route('cases.request.form');
        }

        $clients = Client::with('user')->get();
        $lawyers = Lawyer::with('user')->get();
        return view('cases.create', compact('clients', 'lawyers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'case_number' => ['required', 'string', 'unique:cases'],
            'status'      => ['required', 'in:active,pending,closed,dismissed'],
            'description' => ['nullable', 'string'],
            'client_id'   => ['required', 'exists:clients,id'],
            'lawyer_id'   => ['required', 'exists:lawyers,id'],
            'filed_date'  => ['nullable', 'date'],
        ]);

        LegalCase::create($data);
        return redirect()->route('cases.index')->with('success', 'Case created successfully.');
    }

    public function show(LegalCase $case)
    {
        $case->load(['client.user', 'lawyer.user', 'hearings', 'documents.uploader']);
        return view('cases.show', compact('case'));
    }

    public function edit(LegalCase $case)
    {
        $clients = Client::with('user')->get();
        $lawyers = Lawyer::with('user')->get();
        return view('cases.edit', compact('case', 'clients', 'lawyers'));
    }

    public function update(Request $request, LegalCase $case)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'case_number' => ['required', 'string', 'unique:cases,case_number,' . $case->id],
            'status'      => ['required', 'in:active,pending,closed,dismissed'],
            'description' => ['nullable', 'string'],
            'client_id'   => ['required', 'exists:clients,id'],
            'lawyer_id'   => ['required', 'exists:lawyers,id'],
            'filed_date'  => ['nullable', 'date'],
        ]);

        $case->update($data);
        return redirect()->route('cases.index')->with('success', 'Case updated successfully.');
    }

    public function destroy(LegalCase $case)
    {
        $case->delete();
        return redirect()->route('cases.index')->with('success', 'Case deleted successfully.');
    }

    // ── Client Request Flow ───────────────────────────────────────────────────

    public function requestForm()
    {
        $lawyers = Lawyer::with('user')->get();
        return view('cases.request', compact('lawyers'));
    }

    public function submitRequest(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'lawyer_id'   => ['required', 'exists:lawyers,id'],
        ]);

        LegalCase::create([
            'title'       => $data['title'],
            'case_number' => 'REQ-' . strtoupper(uniqid()),
            'status'      => 'pending',
            'description' => $data['description'],
            'client_id'   => $user->client->id,
            'lawyer_id'   => $data['lawyer_id'],
            'filed_date'  => now(),
        ]);

        return redirect()->route('cases.index')
            ->with('success', 'Your case request has been submitted! Please wait for the lawyer to review it.');
    }

    public function accept(LegalCase $case)
    {
        $case->update(['status' => 'active']);
        return redirect()->route('cases.index')
            ->with('success', 'Case accepted successfully.');
    }

    public function deny(Request $request, LegalCase $case)
    {
        $data = $request->validate([
            'denial_reason' => ['required', 'string', 'max:500'],
        ]);

        $case->update([
            'status'        => 'dismissed',
            'denial_reason' => $data['denial_reason'],
        ]);

        return redirect()->route('cases.index')
            ->with('success', 'Case has been denied.');
    }
}