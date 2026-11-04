<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing');
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

    public function finderHospital(Hospital $hospital): View
    {
        $hospital->load(['bedCategories', 'services' => fn ($q) => $q->where('is_public_visible', true), 'staff']);

        return view('pages.finder.details', [
            'hospital' => $hospital,
        ]);
    }
}
