<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\HospitalResource;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinderController extends Controller
{
    public function index(Request $request): JsonResponse
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

        if ($request->filled('care_type')) {
            $careType = $request->query('care_type');
            if (in_array(strtolower($careType), ['accident', 'trauma', 'emergency', 'general emergency', 'accident / trauma'])) {
                $query->where(function ($q) {
                    $q->where('emergency_status', 'accepting')
                        ->orWhere('emergency_status', 'limited');
                });
            } elseif (str_contains(strtolower($careType), 'maternity')) {
                $query->whereHas('services', function ($sq) {
                    $sq->where('name', 'like', '%maternity%');
                });
            } elseif (str_contains(strtolower($careType), 'child')) {
                $query->whereHas('services', function ($sq) {
                    $sq->where('name', 'like', '%pediatric%')
                        ->orWhere('name', 'like', '%child%');
                });
            }
        }

        if ($request->filled('availability')) {
            $avail = $request->query('availability');
            if ($avail === 'available') {
                $careType = $request->query('care_type');
                if ($careType && in_array(strtolower($careType), ['accident', 'trauma', 'emergency', 'general emergency', 'accident / trauma'])) {
                    $query->whereHas('bedCategories', fn ($q) => $q->where('is_emergency_intake', true)->where('available_beds', '>', 0));
                } elseif ($careType && str_contains(strtolower($careType), 'maternity')) {
                    $query->whereHas('bedCategories', fn ($q) => $q->where('care_category', 'maternity')->where('available_beds', '>', 0));
                } elseif ($careType && str_contains(strtolower($careType), 'child')) {
                    $query->whereHas('bedCategories', fn ($q) => $q->where('care_category', 'pediatric')->where('available_beds', '>', 0));
                } else {
                    $query->whereHas('bedCategories', fn ($q) => $q->where('available_beds', '>', 0));
                }
            }
        }

        $hospitals = $query->get();

        return response()->json([
            'data' => HospitalResource::collection($hospitals),
            'meta' => [
                'total' => $hospitals->count(),
            ],
        ]);
    }

    public function show(string $slugOrId): JsonResponse
    {
        $hospital = Hospital::with([
            'bedCategories',
            'services' => fn ($q) => $q->where('is_public_visible', true),
            'departments',
        ])
            ->where('slug', $slugOrId)
            ->orWhere('id', $slugOrId)
            ->firstOrFail();

        return response()->json([
            'data' => new HospitalResource($hospital),
        ]);
    }

    public function contact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        return response()->json([
            'message' => 'Your message has been sent. We will be in touch soon.',
        ], 200);
    }
}
