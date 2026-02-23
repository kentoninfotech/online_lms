<x-mail::message>
# New Contact Form Submission

Hello Admin,

A new message has been submitted through the contact form on your website.

<x-mail::panel>
**From:** {{ $contact->name }}  
**Email:** {{ $contact->email }}  
**Phone:** {{ $contact->phone ?? '(Not provided)' }}  
**Subject:** {{ $contact->subject ?? '(No subject)' }}  
**Received:** {{ $contact->created_at->format('M d, Y \a\t h:i A') }}
</x-mail::panel>

## Message

{{ $contact->message }}

---

<x-mail::button :url="route('admin.feedback.show', $contact)">
View Full Message & Respond
</x-mail::button>

Thank you!  
{{ config('app.name') }} System
</x-mail::message>
