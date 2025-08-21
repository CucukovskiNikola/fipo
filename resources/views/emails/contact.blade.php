<x-mail::message>
# New Contact Form Submission

You have received a new message from your website contact form.

**From:** {{ $contactData['name'] }}  
**Email:** {{ $contactData['email'] }}  
**Subject:** {{ $contactData['subject'] }}  
**Submitted:** {{ $contactData['submitted_at']->format('F j, Y \a\t g:i A') }}

---

## Message:

{!! nl2br(e($contactData['message'])) !!}

---

**Reply directly to this email to respond to {{ $contactData['name'] }}.**

Best regards,<br>
{{ config('app.name') }} Contact System
</x-mail::message>
