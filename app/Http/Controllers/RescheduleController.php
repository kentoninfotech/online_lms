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
    private RescheduleService $service;

    public function __construct(RescheduleService $service)
    {
        $this->service = $service;
    }

    /**
     * Student/Parent requests reschedule
     */
    public function store(StoreRescheduleRequest $request, LessonOccurrence $occurrence)
    {
        // $this->authorize('request', RescheduleRequest::class);
        
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

        try {
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
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'You have reached your reschedule limit for the month cycle.');
        }


    }

    /**
     * Instructor/Admin approves a request
     */
    public function approve(Request $request, RescheduleRequest $reschedule)
    {
        $user = $request->user();
        
        // Debug information
        \Log::info('User roles:', [
            'user_id' => $user->id,
            'name' => $user->name,
            'roles' => $user->getRoleNames()->toArray(),
            'is_admin' => $user->hasRole('admin'),
            'is_instructor' => $user->hasRole('instructor'),
            'has_any_role' => $user->hasAnyRole(['instructor', 'admin']),
        ]);

        $this->authorize('approve', $reschedule);

        $this->service->approveRequest($reschedule, false, $user);

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

        $this->service->rejectRequest($reschedule, $request->user(), $validated['decision_reason'] ?? null);

        return redirect()
            ->back() 
            ->with('success', 'Reschedule rejected successfully');
    }
}
