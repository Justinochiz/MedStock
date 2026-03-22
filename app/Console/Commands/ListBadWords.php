<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListBadWords extends Command
{
    protected $signature = 'badwords:list';
    protected $description = 'List all configured bad words';

    public function handle()
    {
        $config = config('badwords.words');
        $enabled = config('badwords.enabled');

        $this->info('Bad Words Filter Status: ' . ($enabled ? 'ENABLED' : 'DISABLED'));
        $this->line('');
        $this->info('Configured Bad Words:');
        $this->line('');

        foreach ($config as $word) {
            $this->line("  • {$word}");
        }

        $this->line('');
        $this->info('Total: ' . count($config) . ' bad words');
        $this->line('');
        $this->comment('To add more words, edit config/badwords.php');
    }
}
