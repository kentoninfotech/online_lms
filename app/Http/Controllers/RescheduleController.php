<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\LessonOccurrence;
use App\Models\RescheduleRequest;
use App\Services\RescheduleService;
use App\Http\Requests\StoreRescheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RescheduleController extends Controller
{
    public function __construct(private RescheduleService $service) {}

    /**
     * Student/Parent requests reschedule
     */
    public function store(StoreRescheduleRequest $request, LessonOccurrence $occurrence)
    {
        $this->authorize('request', RescheduleRequest::class);
        
        $user = Auth::user();
        if ($user->user_type === 'parent') {
            $parent = $user;
        }

        // Guard: prevent duplicate requests
        $existing = RescheduleRequest::where('lesson_occurrence_id', $occurrence->id)
            ->where('requested_by', $parent->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()
                ->back()
                ->with('error', 'You already have a pending reschedule request for this lesson.');
        }

        $validated = $request->validated();

        $reschedule = $this->service->requestReschedule(
            $occurrence,
            $request->user(),
            Carbon::parse($validated['proposed_start']),
            $validated['reason'] ?? ''
        );

        if ($reschedule->status === 'approved') {
            return redirect()
                ->back()
                ->with('success', 'Reschedule auto-approved');
        }else{
            return redirect()
                ->back() 
                ->with('success', 'Reschedule request submitted, pending approval');
        }

    }

    /**
     * Instructor/Admin approves a request
     */
    public function approve(Request $request, RescheduleRequest $reschedule)
    {
        $this->authorize('approve', $reschedule);

        $this->service->approveRequest($reschedule, auto: false, approver: $request->user());

        return redirect()
            ->back() 
            ->with('success', 'Reschedule approved successfully');
    }

    /**
     * Instructor/Admin rejects a request
     */
    public function reject(Request $request, RescheduleRequest $reschedule)
    {
        $this->authorize('approve', $reschedule);
        
        $validated = $request->validate([
            'decision_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->rejectRequest($reschedule, approver: $request->user(), reason: $validated['reason'] ?? null);

        return redirect()
            ->back() 
            ->with('success', 'Reschedule rejected successfully');
    }
}
