<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use App\Http\Requests\UploadPaymentEvidenceRequest;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function show(Payment $payment)
    {
        return view('dashboard.show-payment', compact('payment'));
    }

    public function uploadEvidence(UploadPaymentEvidenceRequest $request)
    {
        if (Auth::user()->parent){
            $parent = Auth::user()->parent;
        }else{
            $parent = ParentModel::findOrFail($request->parent_id);
        }

        $subscription = Subscription::findOrFail($request->subscription_id);

        $this->payments->uploadEvidence($subscription, $parent, $request->file('file_path'), $request->amount);

        return redirect()
            ->back()
            ->with('success', 'Payment evidence uploaded. Awaiting admin approval.');
    }

    public function approve(Payment $payment, SubscriptionService $subs)
    {
        $this->payments->approve($payment, $subs);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(Request $request, Payment $payment, SubscriptionService $subs)
    {
        $reason = $request->validate(['decision_reason' => 'required|string|max:300']);

        $this->payments->reject($payment, $subs, $reason['decision_reason']);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment rejected.');
    }

    public function getParentStudentSubscription(ParentModel $parent)
    {
        // Ensure selected user is actually a parent
        if (!$parent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parent selected.'
            ], 400);
        }

        // Get all student IDs linked to this parent
        $studentIds = $parent->students->pluck('id');

        // Handle no linked students
        if ($studentIds->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'This parent has no linked children.'
            ]);
        }

        // Fetch only subscriptions for those students
        $subscriptions = Subscription::whereIn('student_id', $studentIds)
            ->select('id', 'student_id', 'plan_id', 'status')
            ->with(['plan:id,name,price', 'student:id,name'])
            ->get();

        // Handle no subscriptions for linked students
        if ($subscriptions->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'No active subscriptions found for this parent\'s children.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'subscriptions' => $subscriptions
        ]);
    }
}
