<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Student;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(SubscriptionService $subs)
    {
        $this->subs = $subs;
    }

    public function create(Student $student)
    {
        // Check authorization
        $this->authorize('view', $student->subscription);

        $plans = Plan::all();

        return view('dashboard.subscription-plan', compact('student', 'plans'));
    }

    public function store(Request $request, Student $student, Plan $plan)
    {
        // Check authorization
        $this->authorize('view', $student->subscription);

        // use this StoreSubscriptionRequest if decide to use form input 

        // Check if student already has an active subscription
        if ($student->subscription) {
            return redirect()
                ->back()
                ->with('error', 'Student already has an active subscription.');
        }

        $this->subs->createSubscription($student, $plan);

        if (auth()->user()->user_type === 'parent') {
            return redirect()
                ->back()
                ->with('success', 'Subscription created. Please upload payment evidence.');
        }

        return redirect()
            ->route('admin.subscriptions')
            ->with('success', 'Subscription Plan created for ' . $student->name . '. awaiting payment and approval.');
    }

    public function activate(Subscription $subscription)
    {
        // Check authorization
        $this->authorize('update', $subscription);

        if ($subscription->isActive()){
            return redirect()
                ->back()
                ->with('error', 'Subscription is currently active.');
        }
        // Acitve subscription
        $this->subs->activate($subscription);

        return redirect()
                ->back()
                ->with('success', 'Subscription activated successfully.');
    }

    public function cancel(Subscription $subscription)
    {
        // Check authorization
        $this->authorize('update', $subscription);

        $this->subs->reject($subscription);

        return redirect()
                ->back()
                ->with('success', 'Subscription cancelled successfully.');
    }
}
