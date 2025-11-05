<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkMessageRequest;
use App\Services\BulkMessageService;
use App\Models\User;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{

    public function __construct(protected BulkMessageService $bulkMessageService) {}

    public function index()
    {
        return view('dashboard.bulk-messages.index');
    }

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
}
