<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryRequest;
use App\Models\Prescription;
use App\Models\RequestMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $messages = RequestMessage::where('hospital_id', $hospital->id)
            ->with(['sender', 'messageable'])
            ->latest()
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender?->name,
                'sender_role' => $m->sender?->role,
                'messageable_type' => class_basename($m->messageable_type),
                'messageable_id' => $m->messageable_id,
                'reference_code' => $m->messageable?->rx_number ?? $m->messageable?->request_number ?? '—',
                'patient_name' => $m->messageable?->patient?->full_name,
                'message' => $m->message,
                'created_at' => $m->created_at->format('H:i'),
            ]);

        return response()->json([
            'data' => $messages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'messageable_type' => ['required', 'in:prescription,laboratory_request,Prescription,LaboratoryRequest'],
            'messageable_id' => ['required', 'integer'],
            'message' => ['required', 'string'],
        ]);

        $modelClass = str_contains(strtolower($validated['messageable_type']), 'prescription')
            ? Prescription::class
            : LaboratoryRequest::class;

        $message = RequestMessage::create([
            'hospital_id' => $hospital->id,
            'sender_id' => $request->user()->id,
            'messageable_type' => $modelClass,
            'messageable_id' => $validated['messageable_id'],
            'message' => $validated['message'],
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load('sender'),
        ], 201);
    }
}
