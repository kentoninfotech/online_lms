<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessageNotification;
use App\Models\ContactMessage;
use App\Models\ContactResponse;
use App\Notifications\ContactResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    /**
     * Store a contact message from the landing page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $contact = ContactMessage::create([
            ...$validated,
            'status' => 'unread',
        ]);

        // Send admin notification email
        Mail::to(config('mail.from.address'))
            ->send(new NewContactMessageNotification($contact));

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you shortly.');
    }

    /**
     * Admin: List all contact messages
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('isAdmin');

        $status = $request->query('status');
        $query = ContactMessage::with(['latestResponse']);

        if ($status && in_array($status, ['unread', 'read', 'replied'])) {
            $query->where('status', $status);
        }

        $messages = $query->latest()->paginate(20);

        $stats = [
            'unread' => ContactMessage::unread()->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::replied()->count(),
            'total' => ContactMessage::count(),
        ];

        return view('admin.feedback.index', compact('messages', 'stats', 'status'));
    }

    /**
     * Admin: Show single contact message with responses
     */
    public function adminShow(ContactMessage $contact)
    {
        $this->authorize('isAdmin');

        $contact->load(['responses.admin']);
        
        // Mark message as read if it wasn't already
        if ($contact->status === 'unread') {
            $contact->markAsRead();
        }

        return view('admin.feedback.show', compact('contact'));
    }

    /**
     * Admin: Store a response to a contact message
     */
    public function storeResponse(Request $request, ContactMessage $contact)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'response_text' => 'required|string|min:10|max:5000',
        ]);

        try {
            // Get the text content without HTML tags for validation
            $plainText = strip_tags($validated['response_text']);
            $plainText = trim(preg_replace('/\s+/', ' ', $plainText));
            
            // Verify there's actual text content (min 10 characters)
            if (strlen($plainText) < 10) {
                return back()->with('error', 'Response message must contain at least 10 characters of actual text.');
            }

            // Create the response record
            $response = ContactResponse::create([
                'contact_message_id' => $contact->id,
                'admin_id' => Auth::id(),
                'response_text' => $validated['response_text'],
                'responded_at' => now(),
            ]);

            // Mark contact as replied
            $contact->markAsReplied();

            // Send email notification to the sender
            $contact->user = (object) [
                'email' => $contact->email,
                'name' => $contact->name,
            ];

            \Illuminate\Support\Facades\Mail::to($contact->email)->send(
                new \App\Mail\ContactResponseMail($contact, $response)
            );

            return redirect()->back()->with('success', 'Response sent successfully to ' . $contact->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send response: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Delete a contact message
     */
    public function destroy(ContactMessage $contact)
    {
        $this->authorize('isAdmin');

        $contact->delete();

        return redirect()->route('admin.feedback.index')->with('success', 'Message deleted successfully.');
    }

    /**
     * Get unread count for admin header/dashboard
     */
    public static function getUnreadCount()
    {
        return ContactMessage::unread()->count();
    }
}
