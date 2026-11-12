<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function app(): View
    {
        return view('pages.app');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        return back()->with('success', 'Your message has been sent. We will be in touch soon.');
    }

    public function finder(Request $request): View
    {
        return view('pages.finder.index');
    }

    public function finderResults(Request $request): View
    {
        $query = Hospital::with(['bedCategories', 'services' => fn ($q) => $q->where('is_public_visible', true)])
            ->where('status', 'active');

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('lga', 'like', "%{$search}%")
                    ->orWhereHas('services', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $careType = $request->query('care_type', 'General Emergency');

        if ($careType && strtolower($careType) !== 'all') {
            if (in_array(strtolower($careType), ['general emergency', 'accident / trauma', 'child emergency'])) {
                $query->where(function ($q) {
                    $q->where('emergency_status', 'accepting')
                        ->orWhere('emergency_status', 'limited');
                });
            } elseif (strtolower($careType) === 'maternity / delivery') {
                $query->whereHas('services', function ($sq) {
                    $sq->where('name', 'like', '%maternity%');
                });
            }
        }

        $hospitals = $query->get();

        return view('pages.finder.results', [
            'hospitals' => $hospitals,
            'careType' => $careType,
            'searchTerm' => $request->query('q'),
        ]);
    }

    public function finderHospital(Request $request, Hospital $hospital): View
    {
        $hospital->load(['bedCategories', 'services' => fn ($q) => $q->where('is_public_visible', true), 'staff']);
        $careType = $request->query('care_type');

        return view('pages.finder.details', [
            'hospital' => $hospital,
            'careType' => $careType,
            'nearestAlternative' => $hospital->isEmergencyDiverting() ? $hospital->findNearestEmergencyAlternative() : null,
        ]);
    }
}
