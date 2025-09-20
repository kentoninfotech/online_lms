<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Student;
use App\Services\SubscriptionService;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(StoreSubscriptionRequest $request, SubscriptionService $subs)
    {
        $student = Student::findOrFail($request->student_id);
        $plan    = Plan::findOrFail($request->plan_id);

        $subs->createSubscription($student, $plan);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription created. Please upload payment evidence.');
    }
}
