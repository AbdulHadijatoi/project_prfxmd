<?php

namespace App\Services\Logging;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AccountLogger
{
    public static function log($tradeId, $message, $context = [])
    {
        $path = storage_path("logs/accounts/account-{$tradeId}.log");

        $logger = new Logger("account-{$tradeId}");
        $logger->pushHandler(new StreamHandler($path, Logger::INFO));

        $logger->info($message, array_merge([
            'time' => now()->toDateTimeString(),
            'ip'   => request()->ip() ?? 'SYSTEM'
        ], $context));
    }
}
