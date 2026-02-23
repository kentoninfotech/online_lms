@component('mail::message')
# Response to Your Inquiry

Dear {{ $message->name }},

Thank you for reaching out to us. We appreciate your message and have taken the time to review it carefully.

**Your Original Message:**

Subject: {{ $message->subject ?? '(No subject provided)' }}

Message: {{ $message->message }}

---

## Our Response:

@component('mail::panel')
{{ $response->response_text }}
@endcomponent

---

@if($response->responded_at)
**Responded on:** {{ $response->responded_at->format('F j, Y \a\t g:i A') }}
@endif

If you have any further questions or need clarification, please feel free to reach out to us again.

Best regards,

**COINMAC Inc Support Team**

info@coinmac.org | +234 806 563 2882
@endcomponent
