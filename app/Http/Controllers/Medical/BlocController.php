<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\OperatingRoom;
use App\Models\OperatingRoomBooking;
use App\Models\Patient;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlocController extends Controller
{
    public function index(): View
    {
        $bookings = OperatingRoomBooking::with(['operatingRoom', 'patient', 'reservedBy'])
            ->latest('starts_at')
            ->paginate(20);

        $rooms = OperatingRoom::orderBy('name')->get();

        return view('medical.bloc.index', compact('bookings', 'rooms'));
    }

    public function create(): View
    {
        return view('medical.bloc.form', [
            'booking' => new OperatingRoomBooking,
            'rooms' => OperatingRoom::orderBy('name')->get(),
            'patients' => Patient::orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'operating_room_id' => ['required', 'exists:operating_rooms,id'],
            'patient_id' => ['nullable', 'required_without:external_client', 'exists:patients,id'],
            'external_client' => ['nullable', 'required_without:patient_id', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'billing_type' => ['required', 'in:half_day,full_day'],
            'convention_notes' => ['nullable', 'string'],
            'bill_now' => ['boolean'],
        ]);

        $room = OperatingRoom::findOrFail($data['operating_room_id']);
        $amount = $data['billing_type'] === 'full_day'
            ? (float) $room->full_day_rate
            : (float) $room->half_day_rate;

        $sale = null;
        if (($data['bill_now'] ?? true) && $amount > 0 && $data['patient_id']) {
            $patient = Patient::findOrFail($data['patient_id']);
            $sale = $billing->bill($patient, 'bloc', "Location bloc — {$room->name}", $amount);
        }

        OperatingRoomBooking::create([
            'operating_room_id' => $data['operating_room_id'],
            'patient_id' => $data['patient_id'] ?? null,
            'reserved_by' => auth()->id(),
            'sale_id' => $sale?->id,
            'external_client' => $data['external_client'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'billing_type' => $data['billing_type'],
            'amount' => $amount,
            'status' => 'reserved',
            'convention_notes' => $data['convention_notes'] ?? null,
        ]);

        return redirect()->route('medical.bloc.index')->with('status', 'Réservation bloc enregistrée.');
    }
}
