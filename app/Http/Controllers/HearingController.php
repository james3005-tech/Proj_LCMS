<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\LegalCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HearingController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Hearing::with(['legalCase.client.user', 'legalCase.lawyer.user']);

        if ($user->role === 'client' && $user->client) {
            $query->whereHas('legalCase', fn($q) => $q->where('client_id', $user->client->id));
        } elseif ($user->role === 'lawyer' && $user->lawyer) {
            $query->whereHas('legalCase', fn($q) => $q->where('lawyer_id', $user->lawyer->id));
        }

        if ($request->filled('month')) {
            $query->whereMonth('hearing_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('hearing_date', $request->year);
        }

        // For calendar: get all hearings for current month
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $calendarHearings = Hearing::with('legalCase')
            ->whereMonth('hearing_date', $month)
            ->whereYear('hearing_date', $year)
            ->get()
            ->groupBy(fn($h) => Carbon::parse($h->hearing_date)->day);

        $hearings = $query->orderBy('hearing_date', 'desc')->paginate(10);

        $daysInMonth    = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $firstDayOfWeek = Carbon::createFromDate($year, $month, 1)->dayOfWeek;
        $currentMonth   = Carbon::createFromDate($year, $month, 1);

        return view('hearings.index', compact(
            'hearings', 'calendarHearings',
            'month', 'year', 'daysInMonth',
            'firstDayOfWeek', 'currentMonth'
        ));
    }

    public function create()
    {
        $cases = LegalCase::with('client.user')->get();
        return view('hearings.create', compact('cases'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'case_id'      => ['required', 'exists:cases,id'],
            'title'        => ['required', 'string', 'max:255'],
            'hearing_date' => ['required', 'date'],
            'location'     => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string'],
            'status'       => ['required', 'in:scheduled,completed,postponed,cancelled'],
        ]);

        Hearing::create($data);
        return redirect()->route('hearings.index')->with('success', 'Hearing scheduled successfully.');
    }

    public function show(Hearing $hearing)
    {
        $hearing->load(['legalCase.client.user', 'legalCase.lawyer.user']);
        return view('hearings.show', compact('hearing'));
    }

    public function edit(Hearing $hearing)
    {
        $cases = LegalCase::with('client.user')->get();
        return view('hearings.edit', compact('hearing', 'cases'));
    }

    public function update(Request $request, Hearing $hearing)
    {
        $data = $request->validate([
            'case_id'      => ['required', 'exists:cases,id'],
            'title'        => ['required', 'string', 'max:255'],
            'hearing_date' => ['required', 'date'],
            'location'     => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string'],
            'status'       => ['required', 'in:scheduled,completed,postponed,cancelled'],
        ]);

        $hearing->update($data);
        return redirect()->route('hearings.index')->with('success', 'Hearing updated successfully.');
    }

    public function destroy(Hearing $hearing)
    {
        $hearing->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing deleted successfully.');
    }
}