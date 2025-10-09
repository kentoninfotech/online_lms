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
    public function __construct(private PaymentService $payments) {}

    public function show(Payment $payment)
    {
        return view('dashboard.show-payment', compact('payment'));
    }

    public function uploadEvidence(UploadPaymentEvidenceRequest $request)
    {
        $parent = Auth::user()->parent;

        $subscription = Subscription::findOrFail($request->subscription_id);

        $this->payments->uploadEvidence($subscription, $parent, $request->file('file_path'), $request->amount);

        return redirect()
            ->route('parent.payments')
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
}
