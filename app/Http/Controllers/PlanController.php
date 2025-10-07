<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;

class PlanController extends Controller
{
    public function plans()
    {
        $plans = Plan::all();

        return view('dashboard.admin.plans', compact('plans'));
    }

    public function create(StorePlanRequest $request)
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $e) {
            // Flash a marker to identify which modal to re-open
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error_modal_type', 'create');
        }

        $plan = Plan::create($validated);

        return redirect()
                ->route('admin.plans')
                ->with('success', 'Plan Created Successfully!');
    }
    
    public function update(StorePlanRequest $request, Plan $plan)
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $e) {
            // Flash a marker to identify which modal to re-open
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error_modal_type', 'create');
        }

        $plan->update($validated);

        return redirect()
                ->route('admin.plans')
                ->with('success', 'Plan Updated Successfully!');
    }

    public function destroy(Plan $plan)
    {
        // Check for active subscriptions using the custom relationship method
        if ($plan->activeSubscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this plan because it has active subscriptions.');
        }
        // No active subscriptions, proceed with deletion
        $plan->delete();

        return back()->with('success', $plan->name .' deleted Successfully!');
    }
}
