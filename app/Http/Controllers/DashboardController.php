<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\LegalCase;
use App\Models\Lawyer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Common stats for admin/lawyer
        $totalClients   = Client::count();
        $activeCases    = LegalCase::where('status', 'active')->count();
        $totalCases     = LegalCase::count();
        $totalHearings  = Hearing::count();
        $todayHearings  = Hearing::whereDate('hearing_date', Carbon::today())->count();
        $totalDocuments = Document::count();

        // Upcoming hearings (next 7 days)
        $upcomingHearings = Hearing::with(['legalCase.client.user'])
            ->where('hearing_date', '>=', Carbon::now())
            ->where('hearing_date', '<=', Carbon::now()->addDays(7))
            ->where('status', 'scheduled')
            ->orderBy('hearing_date')
            ->take(5)
            ->get();

        // Recent cases
        $recentCases = LegalCase::with(['client.user', 'lawyer.user'])
            ->latest()
            ->take(5)
            ->get();

        // Case status breakdown
        $casesByStatus = [
            'active'    => LegalCase::where('status', 'active')->count(),
            'pending'   => LegalCase::where('status', 'pending')->count(),
            'closed'    => LegalCase::where('status', 'closed')->count(),
            'dismissed' => LegalCase::where('status', 'dismissed')->count(),
        ];

        // If client, show only their data
       if ($user->role === 'client' && $user->client) {
            $activeCases   = LegalCase::where('client_id', $user->client->id)->where('status', 'active')->count();
            $totalCases    = LegalCase::where('client_id', $user->client->id)->count();
            $recentCases   = LegalCase::with(['client.user', 'lawyer.user'])
                ->where('client_id', $user->client->id)
                ->latest()->take(5)->get();
            $upcomingHearings = Hearing::with(['legalCase'])
                ->whereHas('legalCase', fn($q) => $q->where('client_id', $user->client->id))
                ->where('hearing_date', '>=', Carbon::now())
                ->where('status', 'scheduled')
                ->orderBy('hearing_date')
                ->take(5)->get();
        }

        // If lawyer, show only their data
        if ($user->role === 'lawyer' && $user->lawyer) {
            $activeCases   = LegalCase::where('lawyer_id', $user->lawyer->id)->where('status', 'active')->count();
            $totalCases    = LegalCase::where('lawyer_id', $user->lawyer->id)->count();
            $recentCases   = LegalCase::with(['client.user', 'lawyer.user'])
                ->where('lawyer_id', $user->lawyer->id)
                ->latest()->take(5)->get();
            $upcomingHearings = Hearing::with(['legalCase'])
                ->whereHas('legalCase', fn($q) => $q->where('lawyer_id', $user->lawyer->id))
                ->where('hearing_date', '>=', Carbon::now())
                ->where('status', 'scheduled')
                ->orderBy('hearing_date')
                ->take(5)->get();
        }

        return view('dashboard.index', compact(
            'totalClients', 'activeCases', 'totalCases',
            'totalHearings', 'todayHearings', 'totalDocuments',
            'upcomingHearings', 'recentCases', 'casesByStatus'
        ));
    }
}