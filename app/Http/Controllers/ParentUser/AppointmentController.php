<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentRequestMail;
use App\Models\Appointment;
use App\Models\Baby;
use App\Models\MidwifeAvailability;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List all appointments for the parent.
     */
    public function index(Request $request)
    {
        $parentId = Auth::guard('parent')->id();
        $status = $request->get('status', 'all');

        $query = Appointment::with(['midwife', 'baby'])
            ->where('parent_id', $parentId)
            ->orderBy('appointment_date', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $appointments = $query->get();

        return view('back.pages.parent.appointments.index', [
            'pageTitle' => 'My Appointments',
            'appointments' => $appointments,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Booking form.
     */
    public function create()
    {
        $parentId = Auth::guard('parent')->id();
        $babies = Baby::where('parent_id', $parentId)->get();

        return view('back.pages.parent.appointments.book', [
            'pageTitle' => 'Book Appointment',
            'babies' => $babies,
        ]);
    }

    /**
     * Store a new appointment booking.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'baby_id' => 'required|exists:baby,baby_id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required',
                'reason' => 'required|string|min:10|max:500',
            ]);

            $parentId = Auth::guard('parent')->id();
            $parent = Auth::guard('parent')->user();

            // Verify baby belongs to parent
            $baby = Baby::where('baby_id', $request->baby_id)
                        ->where('parent_id', $parentId)
                        ->first();

            if (!$baby || !$baby->midwife_id) {
                return redirect()->back()->withInput()
                    ->with('fail', 'No midwife assigned to this baby.');
            }

            $midwifeId = $baby->midwife_id;

            // Check max 60 days ahead
            if (\Carbon\Carbon::parse($request->appointment_date)->gt(now()->addDays(60))) {
                return redirect()->back()->withInput()
                    ->with('fail', 'Cannot book more than 60 days in advance.');
            }

            // Check for overlapping appointments
            $overlap = Appointment::where('midwife_id', $midwifeId)
                ->where('appointment_date', $request->appointment_date)
                ->where('status', '!=', 'rejected')
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($request) {
                    $time = $request->appointment_time;
                    $q->whereRaw("ABS(TIMESTAMPDIFF(MINUTE, appointment_time, ?)) < 30", [$time]);
                })
                ->exists();

            if ($overlap) {
                return redirect()->back()->withInput()
                    ->with('fail', 'This time slot is not available. Please choose another time.');
            }

            $appointment = Appointment::create([
                'parent_id' => $parentId,
                'midwife_id' => $midwifeId,
                'baby_id' => $request->baby_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // Notify midwife
            $this->notificationService->notifyMidwife(
                $midwifeId,
                'appointment_request',
                'New Appointment Request',
                "Parent {$parent->name} has requested an appointment on " . $appointment->appointment_date->format('M d, Y') . " at {$appointment->appointment_time}.",
                ['appointment_id' => $appointment->id]
            );

            // Send email to midwife
            try {
                $midwife = $baby->midwife;
                Mail::to($midwife->email)->send(new AppointmentRequestMail(
                    $parent->name,
                    $appointment->appointment_date->format('M d, Y'),
                    $appointment->appointment_time,
                    $appointment->reason,
                    $baby->full_name
                ));
            } catch (\Exception $e) {
                \Log::error('Failed to send appointment request email', ['error' => $e->getMessage()]);
            }

            return redirect()->route('parent.appointment.index')
                ->with('success', 'Appointment booked successfully! Waiting for midwife confirmation.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('fail', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show appointment detail.
     */
    public function show($id)
    {
        $parentId = Auth::guard('parent')->id();
        $appointment = Appointment::with(['midwife', 'baby', 'chatRoom'])
            ->where('id', $id)
            ->where('parent_id', $parentId)
            ->firstOrFail();

        return view('back.pages.parent.appointments.show', [
            'pageTitle' => 'Appointment Details',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Cancel a pending appointment.
     */
    public function destroy($id)
    {
        $parentId = Auth::guard('parent')->id();
        $appointment = Appointment::where('id', $id)
            ->where('parent_id', $parentId)
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Notify midwife
        $this->notificationService->notifyMidwife(
            $appointment->midwife_id,
            'appointment_cancelled',
            'Appointment Cancelled',
            "The appointment on " . $appointment->appointment_date->format('M d, Y') . " has been cancelled by the parent.",
            ['appointment_id' => $appointment->id]
        );

        return redirect()->route('parent.appointment.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Get midwife availability for AJAX calendar.
     */
    public function getAvailability(Request $request)
    {
        $parentId = Auth::guard('parent')->id();
        $babyId = $request->get('baby_id');

        if (!$babyId) {
            return response()->json(['status' => 0, 'msg' => 'Baby ID required.']);
        }

        $baby = Baby::where('baby_id', $babyId)
                    ->where('parent_id', $parentId)
                    ->first();

        if (!$baby || !$baby->midwife_id) {
            return response()->json(['status' => 0, 'msg' => 'No midwife assigned.']);
        }

        $availability = MidwifeAvailability::where('midwife_id', $baby->midwife_id)
            ->where('is_active', true)
            ->get();

        // Get existing appointments for the midwife
        $existingAppointments = Appointment::where('midwife_id', $baby->midwife_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->get(['appointment_date', 'appointment_time']);

        return response()->json([
            'status' => 1,
            'data' => [
                'availability' => $availability,
                'booked_slots' => $existingAppointments,
                'midwife_name' => $baby->midwife->name ?? 'Unknown',
            ],
        ]);
    }
}
