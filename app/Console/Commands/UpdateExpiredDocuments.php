<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NonManfeeDocument;
use Carbon\Carbon;

class UpdateExpiredDocuments extends Command
{
    protected $signature = 'documents:update-expired';
    protected $description = 'Update is_active to 0 if expired_at is past the current time';

    public function handle()
    {
        // Ambil dokumen yang expired dan masih aktif
        $expiredDocuments = NonManfeeDocument::where('expired_at', '<', Carbon::now())
            ->where('is_active', 1) // Hanya update yang masih aktif
            ->get();

        foreach ($expiredDocuments as $document) {
            $document->update(['is_active' => 0]);
        }

        $this->info(count($expiredDocuments) . ' documents have been marked as inactive.');
    }
}