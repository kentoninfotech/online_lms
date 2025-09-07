<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function uploadEvidence(Request $request, PaymentService $payments)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'amount'          => 'required|numeric|min:1',
            'evidence'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);
        $parent = Auth::user();

        $payments->uploadEvidence($subscription, $parent, $request->file('evidence'), $request->amount);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Payment evidence uploaded. Awaiting admin approval.');
    }

    public function approve(Payment $payment, PaymentService $payments, SubscriptionService $subs)
    {
        $payments->approve($payment, Auth::user(), $subs);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(Request $request, Payment $payment, PaymentService $payments, SubscriptionService $subs)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $payments->reject($payment, Auth::user(), $request->reason, $subs);

        return redirect()
            ->route('admin.payments.index')
            ->with('error', 'Payment rejected.');
    }
}
