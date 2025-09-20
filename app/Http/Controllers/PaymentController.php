<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use App\Http\Requests\UploadPaymentEvidenceRequest;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function uploadEvidence(UploadPaymentEvidenceRequest $request, PaymentService $payments)
    {
        $subscription = Subscription::findOrFail($request->subscription_id);
        $parent = Auth::user();

        $payments->uploadEvidence($subscription, $parent, $request->file('evidence'), $request->amount);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Payment evidence uploaded. Awaiting admin approval.');
    }

    public function approve(Payment $payment, PaymentService $payments, SubscriptionService $subs)
    {
        $payments->approve($payment, $subs);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(Payment $payment, PaymentService $payments, SubscriptionService $subs)
    {
        $payments->reject($payment, $subs);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment rejected.');
    }
}
