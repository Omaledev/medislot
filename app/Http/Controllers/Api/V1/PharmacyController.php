<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MedicationResource;
use App\Http\Resources\V1\PrescriptionResource;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\RequestMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $newRx = Prescription::where('hospital_id', $hospital->id)->where('status', 'new')->count();
        $awaitingDispensing = Prescription::where('hospital_id', $hospital->id)->whereIn('status', ['accepted', 'processing'])->count();
        $partiallyDispensed = Prescription::where('hospital_id', $hospital->id)->where('status', 'partially_dispensed')->count();
        $clarificationNeeded = Prescription::where('hospital_id', $hospital->id)->where('status', 'clarification_needed')->count();
        $dispensedToday = Prescription::where('hospital_id', $hospital->id)
            ->where('status', 'dispensed')
            ->whereDate('dispensed_at', today())
            ->count();
        $lowStockItems = Medication::where('hospital_id', $hospital->id)->where('status', 'low_stock')->count();

        $recentRx = Prescription::where('hospital_id', $hospital->id)
            ->with(['patient', 'prescriber', 'items'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'stats' => [
                'new_prescriptions' => $newRx,
                'awaiting_dispensing' => $awaitingDispensing,
                'partially_dispensed' => $partiallyDispensed,
                'clarification_needed' => $clarificationNeeded,
                'dispensed_today' => $dispensedToday,
                'low_stock_items' => $lowStockItems,
            ],
            'recent_prescriptions' => PrescriptionResource::collection($recentRx),
        ]);
    }

    public function prescriptions(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $query = Prescription::where('hospital_id', $hospital->id)->with(['patient', 'prescriber', 'items.medication']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $prescriptions = $query->latest()->get();

        return response()->json([
            'data' => PrescriptionResource::collection($prescriptions),
        ]);
    }

    public function showPrescription(Prescription $prescription): JsonResponse
    {
        $prescription->load(['patient', 'prescriber', 'items.medication', 'visit']);

        return response()->json([
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    public function acceptPrescription(Request $request, Prescription $prescription): JsonResponse
    {
        $prescription->update([
            'status' => 'accepted',
            'accepted_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Prescription accepted',
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    public function beginDispensing(Request $request, Prescription $prescription): JsonResponse
    {
        $prescription->update([
            'status' => 'processing',
        ]);

        return response()->json([
            'message' => 'Dispensing started',
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    public function dispensePrescription(Request $request, Prescription $prescription): JsonResponse
    {
        // Deduct inventory where medication_id exists
        foreach ($prescription->items as $item) {
            if ($item->medication_id) {
                $med = Medication::find($item->medication_id);
                if ($med) {
                    $med->decrement('quantity', min($med->quantity, $item->quantity_prescribed));
                    $med->updateStatus();
                }
            }
            $item->update([
                'status' => 'dispensed',
                'quantity_dispensed' => $item->quantity_prescribed,
            ]);
        }

        $prescription->update([
            'status' => 'dispensed',
            'dispensed_by' => $request->user()->id,
            'dispensed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Prescription dispensed successfully',
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    public function requestClarification(Request $request, Prescription $prescription): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $prescription->update([
            'status' => 'clarification_needed',
            'clarification_note' => $validated['note'],
        ]);

        RequestMessage::create([
            'hospital_id' => $prescription->hospital_id,
            'sender_id' => $request->user()->id,
            'messageable_type' => Prescription::class,
            'messageable_id' => $prescription->id,
            'message' => $validated['note'],
        ]);

        return response()->json([
            'message' => 'Clarification requested from prescriber',
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    public function inventory(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $query = Medication::where('hospital_id', $hospital->id);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('generic_name', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('name')->get();

        return response()->json([
            'data' => MedicationResource::collection($items),
            'meta' => [
                'total' => $items->count(),
                'low_stock' => $items->where('status', 'low_stock')->count(),
                'out_of_stock' => $items->where('status', 'out_of_stock')->count(),
                'expired' => $items->where('status', 'expired')->count(),
            ],
        ]);
    }

    public function storeMedication(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'generic_name' => ['nullable', 'string'],
            'strength' => ['nullable', 'string'],
            'form' => ['required', 'string'],
            'unit_of_measurement' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'batch_number' => ['nullable', 'string'],
        ]);

        $nextNum = Medication::where('hospital_id', $hospital->id)->count() + 1;
        $code = 'MED-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);

        $med = Medication::create([
            'hospital_id' => $hospital->id,
            'code' => $code,
            ...$validated,
            'status' => 'available',
        ]);
        $med->updateStatus();

        return response()->json([
            'message' => 'Medication added to inventory',
            'data' => new MedicationResource($med),
        ], 201);
    }

    public function showMedication(Medication $medication): JsonResponse
    {
        return response()->json([
            'data' => new MedicationResource($medication),
        ]);
    }

    public function receiveStock(Request $request, Medication $medication): JsonResponse
    {
        $validated = $request->validate([
            'quantity_received' => ['required', 'integer', 'min:1'],
            'batch_number' => ['nullable', 'string'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $medication->increment('quantity', $validated['quantity_received']);

        $updates = [];
        if (! empty($validated['batch_number'])) {
            $updates['batch_number'] = $validated['batch_number'];
        }
        if (! empty($validated['expiry_date'])) {
            $updates['expiry_date'] = $validated['expiry_date'];
        }
        if ($updates) {
            $medication->update($updates);
        }
        $medication->updateStatus();

        return response()->json([
            'message' => 'Stock received successfully',
            'data' => new MedicationResource($medication),
        ]);
    }

    public function adjustStock(Request $request, Medication $medication): JsonResponse
    {
        $validated = $request->validate([
            'adjustment_amount' => ['required', 'integer'],
            'reason' => ['required', 'string'],
        ]);

        $newQty = max(0, $medication->quantity + $validated['adjustment_amount']);
        $medication->update(['quantity' => $newQty]);
        $medication->updateStatus();

        return response()->json([
            'message' => 'Stock count adjusted successfully',
            'data' => new MedicationResource($medication),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'title' => $user->title,
            ],
        ]);
    }
}
