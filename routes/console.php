<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Prescription;
use Illuminate\Support\Facades\Schedule;

// Command to clean up old completed/rejected prescription tickets and files
Artisan::command('prescription:cleanup {days=30}', function () {
    $days = (int) $this->argument('days');
    $date = now()->subDays($days);

    // Fetch old completed/rejected tickets
    $tickets = Prescription::whereIn('status', ['completed', 'rejected'])
        ->where('updated_at', '<', $date)
        ->get();

    $count = $tickets->count();

    if ($count === 0) {
        $this->info("Tidak ada tiket resep lama (>= {$days} hari) yang perlu dibersihkan.");
        return;
    }

    foreach ($tickets as $ticket) {
        // Deleting the model triggers the booted 'deleted' event, which cleans up the local storage file
        $ticket->delete();
    }

    $this->info("Berhasil membersihkan {$count} tiket resep lama beserta foto dan riwayat chat-nya.");
})->purpose('Clean up completed or rejected prescription tickets and their uploaded files older than N days');

// Schedule the cleanup command to run daily
Schedule::command('prescription:cleanup 30')->daily();

