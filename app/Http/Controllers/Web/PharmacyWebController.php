<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryRequest;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\RequestMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacyWebController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $newRxCount = Prescription::where('hospital_id', $hospital->id)->where('status', 'new')->count();
        $awaitingDispensingCount = Prescription::where('hospital_id', $hospital->id)->whereIn('status', ['accepted', 'processing'])->count();
        $partiallyDispensedCount = Prescription::where('hospital_id', $hospital->id)->where('status', 'partially_dispensed')->count();
        $clarificationNeededCount = Prescription::where('hospital_id', $hospital->id)->where('status', 'clarification_needed')->count();
        $dispensedTodayCount = Prescription::where('hospital_id', $hospital->id)
            ->where('status', 'dispensed')
            ->whereDate('dispensed_at', today())
            ->count();
        $lowStockCount = Medication::where('hospital_id', $hospital->id)->where('status', 'low_stock')->count();

        $prescriptions = Prescription::where('hospital_id', $hospital->id)
            ->with(['patient', 'prescriber', 'items.medication'])
            ->latest()
            ->take(8)
            ->get();

        return view('portals.pharmacy.dashboard', compact(
            'hospital',
            'user',
            'newRxCount',
            'awaitingDispensingCount',
            'partiallyDispensedCount',
            'clarificationNeededCount',
            'dispensedTodayCount',
            'lowStockCount',
            'prescriptions'
        ));
    }

    public function prescriptions(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $query = Prescription::where('hospital_id', $hospital->id)->with(['patient', 'prescriber', 'items.medication']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $prescriptions = $query->latest()->get();

        return view('portals.pharmacy.prescriptions', compact('hospital', 'user', 'prescriptions'));
    }

    public function showPrescription(Prescription $prescription): View
    {
        $prescription->load(['patient', 'prescriber', 'items.medication', 'visit']);
        $hospital = $prescription->hospital;
        $user = auth()->user();

        return view('portals.pharmacy.prescription-details', compact('hospital', 'user', 'prescription'));
    }

    public function acceptPrescription(Prescription $prescription): RedirectResponse
    {
        $prescription->update([
            'status' => 'accepted',
            'accepted_by' => auth()->id(),
        ]);

        return back()->with('success', "Prescription {$prescription->rx_number} accepted.");
    }

    public function beginDispensing(Prescription $prescription): RedirectResponse
    {
        $prescription->update([
            'status' => 'processing',
        ]);

        return back()->with('success', "Dispensing started for {$prescription->rx_number}.");
    }

    public function dispensePrescription(Prescription $prescription): RedirectResponse
    {
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
            'dispensed_by' => auth()->id(),
            'dispensed_at' => now(),
        ]);

        return redirect()->route('pharmacy.dispensing')->with('success', "Prescription {$prescription->rx_number} dispensed successfully.");
    }

    public function requestClarification(Request $request, Prescription $prescription): RedirectResponse
    {
        $validated = $request->validate([
            'clarification_note' => ['required', 'string'],
        ]);

        $prescription->update([
            'status' => 'clarification_needed',
            'clarification_note' => $validated['clarification_note'],
        ]);

        RequestMessage::create([
            'hospital_id' => $prescription->hospital_id,
            'sender_id' => $request->user()->id,
            'messageable_type' => Prescription::class,
            'messageable_id' => $prescription->id,
            'message' => $validated['clarification_note'],
        ]);

        return redirect()->route('pharmacy.prescriptions')->with('success', 'Clarification request sent to consultant.');
    }

    public function dispensingQueue(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $tab = $request->query('tab', 'ready');
        $query = Prescription::where('hospital_id', $hospital->id)->with(['patient', 'items.medication']);

        if ($tab === 'ready') {
            $query->whereIn('status', ['new', 'accepted']);
        } elseif ($tab === 'processing') {
            $query->where('status', 'processing');
        } elseif ($tab === 'partially_dispensed') {
            $query->where('status', 'partially_dispensed');
        }

        $prescriptions = $query->latest()->get();

        $readyCount = Prescription::where('hospital_id', $hospital->id)->whereIn('status', ['new', 'accepted'])->count();
        $processingCount = Prescription::where('hospital_id', $hospital->id)->where('status', 'processing')->count();
        $partiallyCount = Prescription::where('hospital_id', $hospital->id)->where('status', 'partially_dispensed')->count();

        return view('portals.pharmacy.dispensing-queue', compact(
            'hospital',
            'user',
            'prescriptions',
            'tab',
            'readyCount',
            'processingCount',
            'partiallyCount'
        ));
    }

    public function dispensingHistory(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $prescriptions = Prescription::where('hospital_id', $hospital->id)
            ->where('status', 'dispensed')
            ->with(['patient', 'prescriber', 'items.medication', 'dispensedByUser'])
            ->latest('dispensed_at')
            ->paginate(15);

        return view('portals.pharmacy.dispensing-history', compact('hospital', 'user', 'prescriptions'));
    }

    public function inventory(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $query = Medication::where('hospital_id', $hospital->id);

        $filter = $request->query('filter', 'all');
        if ($filter === 'low_stock') {
            $query->where('status', 'low_stock');
        } elseif ($filter === 'out_of_stock') {
            $query->where('status', 'out_of_stock');
        } elseif ($filter === 'expiring_soon') {
            $query->where('expiry_date', '<=', now()->addMonths(3))->where('expiry_date', '>', now());
        } elseif ($filter === 'expired') {
            $query->where('status', 'expired');
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('generic_name', 'like', "%{$q}%");
            });
        }

        $medications = $query->orderBy('name')->get();

        $allMedications = Medication::where('hospital_id', $hospital->id)->get();
        $totalCount = $allMedications->count();
        $lowStockCount = $allMedications->where('status', 'low_stock')->count();
        $outOfStockCount = $allMedications->where('status', 'out_of_stock')->count();
        $expiringCount = $allMedications->filter(fn ($m) => $m->expiry_date && $m->expiry_date->lte(now()->addMonths(3)) && $m->expiry_date->gt(now()))->count();
        $expiredCount = $allMedications->where('status', 'expired')->count();

        return view('portals.pharmacy.inventory', compact(
            'hospital',
            'user',
            'medications',
            'filter',
            'totalCount',
            'lowStockCount',
            'outOfStockCount',
            'expiringCount',
            'expiredCount'
        ));
    }

    public function createMedication(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $user = $request->user();

        return view('portals.pharmacy.add-medication', compact('hospital', 'user'));
    }

    public function storeMedication(Request $request): RedirectResponse
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

        return redirect()->route('pharmacy.inventory')->with('success', "Medication {$med->name} added to inventory.");
    }

    public function messages(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $threads = RequestMessage::where('hospital_id', $hospital->id)
            ->with(['sender', 'messageable.patient'])
            ->latest()
            ->get();

        $activeThread = $threads->first();

        return view('portals.pharmacy.messages', compact('hospital', 'user', 'threads', 'activeThread'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'messageable_type' => ['required', 'string'],
            'messageable_id' => ['required', 'integer'],
            'message' => ['required', 'string'],
        ]);

        $modelClass = str_contains($validated['messageable_type'], 'Prescription') ? Prescription::class : LaboratoryRequest::class;

        RequestMessage::create([
            'hospital_id' => $hospital->id,
            'sender_id' => $request->user()->id,
            'messageable_type' => $modelClass,
            'messageable_id' => $validated['messageable_id'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Reply sent to consultant');
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        return view('portals.pharmacy.profile', compact('hospital', 'user'));
    }
}
