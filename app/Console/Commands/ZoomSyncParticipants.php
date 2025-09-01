<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ZoomSession;
use App\Jobs\SyncZoomParticipantsJob;

class ZoomSyncParticipants extends Command
{
    protected $signature = 'zoom:sync-participants';
    protected $description = 'Queue Zoom participant sync jobs for ended or stale sessions';

    public function handle()
    {
        $sessions = ZoomSession::where(function ($q) {
            $q->where('status', 'ended')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'started')->where('created_at', '<', now()->subHours(3));
              })
              ->orWhere(function ($q3) {
                  $q3->where('status', 'scheduled')->where('created_at', '<', now()->subDay());
              });
        })->get();

        $this->info("Dispatching sync jobs for {$sessions->count()} sessions...");

        foreach ($sessions as $session) {
            $fallback = in_array($session->status, ['started','scheduled']);
            SyncZoomParticipantsJob::dispatch($session, $fallback)->onQueue('zoom-sync');
        }
    }
}
