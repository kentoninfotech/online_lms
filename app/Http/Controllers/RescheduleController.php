<?php

namespace App\Http\Controllers;

use App\Models\LessonOccurrence;
use App\Models\RescheduleRequest;
use App\Services\RescheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RescheduleController extends Controller
{
    public function __construct(private RescheduleService $service) {}

    /**
     * Student/Parent requests reschedule
     */
    public function store(Request $request, LessonOccurrence $occurrence)
    {
        $validated = $request->validate([
            'proposed_start' => ['required', 'date', 'after:now'],
            'reason'         => ['nullable', 'string', 'max:500'],
        ]);

        $reschedule = $this->service->requestReschedule(
            $occurrence,
            $request->user(),
            Carbon::parse($validated['proposed_start']),
            $validated['reason'] ?? ''
        );

        if ($reschedule->status === 'approved') {
            return redirect()
                ->route('lessons.schedule') // REMEMBER TO UPDATE ROUTE (MSD) TO SELF
                ->with('success', 'Reschedule auto-approved');
        }else{
            return redirect()
                ->route('lessons.schedule') // REMEMBER TO UPDATE ROUTE (MSD) TO SELF
                ->with('success', 'Reschedule request submitted, pending approval');
        }

    }

    /**
     * Instructor/Admin approves a request
     */
    public function approve(Request $request, RescheduleRequest $reschedule)
    {
        $this->service->approveRequest($reschedule, auto: false, approver: $request->user());

        return redirect()
            ->route('reschedule.requests') // REMEMBER TO UPDATE ROUTE (MSD) TO SELF
            ->with('success', 'Reschedule approved successfully');
    }

    /**
     * Instructor/Admin rejects a request
     */
    public function reject(Request $request, RescheduleRequest $reschedule)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->rejectRequest($reschedule, approver: $request->user(), reason: $validated['reason'] ?? null);

        return redirect()
            ->route('reschedule.requests') // REMEMBER TO UPDATE ROUTE (MSD) TO SELF
            ->with('success', 'Reschedule rejected successfully');
    }
}
