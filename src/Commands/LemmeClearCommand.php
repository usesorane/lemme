<?php

namespace Sorane\Lemme\Commands;

use Illuminate\Console\Command;
use Sorane\Lemme\Facades\Lemme;

class LemmeClearCommand extends Command
{
    public $signature = 'lemme:clear';

    public $description = 'Clear Lemme documentation cache';

    public function handle(): int
    {
        Lemme::clearCache();

        $this->info('Documentation cache cleared successfully!');

        return self::SUCCESS;
    }
}
