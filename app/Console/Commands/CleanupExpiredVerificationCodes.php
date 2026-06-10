<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PhoneVerificationService;

class CleanupExpiredVerificationCodes extends Command
{
    protected $signature = 'verification:cleanup';
    protected $description = 'Clean up expired phone verification codes';

    public function handle()
    {
        app(PhoneVerificationService::class)->cleanupExpiredCodes();
        $this->info('Expired verification codes cleaned up successfully.');
    }
}