<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentConfirmedMail;
use App\Mail\AppointmentRejectedMail;
use App\Models\Appointment;
use App\Models\ChatRoom;
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
     * List all appointments for the midwife.
     */
    public function index(Request $request)
    {
        $midwifeId = Auth::guard('midwife')->id();
        $status = $request->get('status', 'all');

        $query = Appointment::with(['parentUser', 'baby'])
            ->where('midwife_id', $midwifeId)
            ->orderBy('appointment_date', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $appointments = $query->get();

        return view('back.pages.midwife.appointments.index', [
            'pageTitle' => 'Appointments',
            'appointments' => $appointments,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Show appointment detail.
     */
    public function show($id)
    {
        $midwifeId = Auth::guard('midwife')->id();
        $appointment = Appointment::with(['parentUser', 'baby', 'chatRoom'])
            ->where('id', $id)
            ->where('midwife_id', $midwifeId)
            ->firstOrFail();

        return view('back.pages.midwife.appointments.show', [
            'pageTitle' => 'Appointment Details',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Confirm an appointment.
     */
    public function confirm($id)
    {
        $midwifeId = Auth::guard('midwife')->id();
        $midwife = Auth::guard('midwife')->user();

        $appointment = Appointment::where('id', $id)
            ->where('midwife_id', $midwifeId)
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Auto-create chat room
        ChatRoom::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'parent_id' => $appointment->parent_id,
                'midwife_id' => $appointment->midwife_id,
                'status' => 'active',
            ]
        );

        // Notify parent
        $this->notificationService->notifyParent(
            $appointment->parent_id,
            'appointment_confirmed',
            'Appointment Confirmed',
            "Your appointment on " . $appointment->appointment_date->format('M d, Y') . " at {$appointment->appointment_time} has been confirmed by Midwife {$midwife->name}.",
            ['appointment_id' => $appointment->id]
        );

        // Send email
        try {
            $parent = $appointment->parentUser;
            Mail::to($parent->email)->send(new AppointmentConfirmedMail(
                $midwife->name,
                $appointment->appointment_date->format('M d, Y'),
                $appointment->appointment_time
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment confirmed email', ['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Appointment confirmed successfully.');
    }

    /**
     * Reject an appointment.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $midwifeId = Auth::guard('midwife')->id();
        $midwife = Auth::guard('midwife')->user();

        $appointment = Appointment::where('id', $id)
            ->where('midwife_id', $midwifeId)
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify parent
        $this->notificationService->notifyParent(
            $appointment->parent_id,
            'appointment_rejected',
            'Appointment Declined',
            "Your appointment on " . $appointment->appointment_date->format('M d, Y') . " was declined: {$request->rejection_reason}",
            ['appointment_id' => $appointment->id]
        );

        // Send email
        try {
            $parent = $appointment->parentUser;
            Mail::to($parent->email)->send(new AppointmentRejectedMail(
                $midwife->name,
                $appointment->appointment_date->format('M d, Y'),
                $appointment->appointment_time,
                $request->rejection_reason
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment rejected email', ['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Appointment rejected.');
    }

    /**
     * Mark appointment as complete.
     */
    public function complete($id)
    {
        $midwifeId = Auth::guard('midwife')->id();

        $appointment = Appointment::where('id', $id)
            ->where('midwife_id', $midwifeId)
            ->where('status', 'confirmed')
            ->firstOrFail();

        $appointment->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Appointment marked as completed.');
    }

    /**
     * Edit availability page.
     */
    public function editAvailability()
    {
        $midwifeId = Auth::guard('midwife')->id();
        $availability = MidwifeAvailability::where('midwife_id', $midwifeId)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('back.pages.midwife.availability', [
            'pageTitle' => 'Manage Availability',
            'availability' => $availability,
        ]);
    }

    /**
     * Save availability.
     */
    public function saveAvailability(Request $request)
    {
        $midwifeId = Auth::guard('midwife')->id();
        $days = $request->input('days', []);

        // Delete existing
        MidwifeAvailability::where('midwife_id', $midwifeId)->delete();

        foreach ($days as $day => $data) {
            if (isset($data['active']) && $data['active'] && !empty($data['start_time']) && !empty($data['end_time'])) {
                MidwifeAvailability::create([
                    'midwife_id' => $midwifeId,
                    'day_of_week' => $day,
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Availability updated successfully.');
    }
}
