<?php

namespace Sorane\Lemme\Commands;

use Illuminate\Console\Command;

class LemmeCommand extends Command
{
    public $signature = 'lemme';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
