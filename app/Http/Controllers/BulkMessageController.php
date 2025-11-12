<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Http\Requests\BulkMessageRequest;
use App\Services\BulkMessageService;
use App\Models\User;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{
    /**
     * Constructor with BulkMessageService injection.
     */
    public function __construct(protected BulkMessageService $bulkMessageService) {}

    public function index()
    {
        return view('dashboard.bulk-messages.index');
    }

    /**
     * Fetch recipients based on role.
     */
    public function fetchRecipients(Request $request)
    {
        $role = $request->get('role');

        if (!in_array($role, ['student', 'parent', 'instructor'])) {
            return response()->json(['error' => 'Invalid role'], 400);
        }

        $users = User::role($role)
        ->with($role . ':id,user_id,number') // eager load related model
        ->select('id', 'name', 'email')
        ->orderBy('name')
        ->get();

        $users = $users->map(function ($user) use ($role) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'number' => optional($user->{$role})->number,
            ];
        });

        return response()->json($users);
    }

    /**
     * Handle sending bulk messages.
     */
    public function send(BulkMessageRequest $request)
    {
        $this->bulkMessageService->dispatchBulkMessage(
            $request->subject,
            $request->message,
            $request->method,
            $request->recipients
        );

        return back()->with('success', 'Messages have been queued for delivery.');
    }

    /**
     * Display recent bulk message logs.
     */
    public function logs()
    {
        $logs = BulkMessage::withCount([
            'recipients as sent_count' => function ($q) {
                $q->where('delivery_status', 'sent');
            },
            'recipients as failed_count' => function ($q) {
                $q->where('delivery_status', 'failed');
            },
            'recipients as total_count'
        ])
        ->orderByDesc('created_at')
        ->take(10)
        ->get()
        ->map(function ($msg) {
            return [
                'id' => $msg->id,
                'subject' => $msg->subject,
                'methods' => $msg->methods,
                'total' => $msg->total_count,
                'sent' => $msg->sent_count,
                'failed' => $msg->failed_count,
                'status' => $msg->status,
                'created_at' => $msg->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json($logs);
    }

    /**
     * Display recipients for a specific bulk message.
     */
    public function recipients($id)
    {
        $recipients = BulkMessageRecipient::where('bulk_message_id', $id)
            ->with('user:id,name,email')
            ->get()
            ->map(function ($r) {
                return [
                    'name' => $r->user->name ?? 'Unknown',
                    'email' => $r->user->email ?? '-',
                    'number' => $r->number,
                    'status' => $r->delivery_status,
                    'updated_at' => $r->updated_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json($recipients);
    }
}
