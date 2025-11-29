<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeDomainCommand extends Command
{
    protected $signature = 'make:domain
        {domain : Domain name, e.g. Category, Tag, Post}';

    protected $description = 'Generate DTO, Filter, Repository, Binding and Service for a domain';

    public function handle(): int
    {
        $domain = ucfirst($this->argument('domain'));

        $this->info("Scaffolding domain: {$domain}");
        $this->newLine();

        // 1. DTO
        $this->callSilent('make:dto', [
            'domain' => $domain,
        ]);
        $this->info('✔ DTO created');

        // 2. Filter
        $this->callSilent('make:filter', [
            'domain' => $domain,
        ]);
        $this->info('✔ Filter created');

        // 3. Repository (interface + eloquent repo)
        $this->callSilent('make:repository', [
            'domain' => $domain,
        ]);
        $this->info('✔ Repository + Interface created');

        // 4. Bind interface ↔ repository
        $this->callSilent('make:bind-repository', [
            'domain' => $domain,
        ]);
        $this->info('✔ Repository binding registered');

        // 5. Service
        $this->callSilent('make:service-domain', [
            'domain' => $domain,
        ]);
        $this->info('✔ Service created');

        $this->newLine();
        $this->info("✅ Done! Domain [{$domain}] scaffolded.");

        return self::SUCCESS;
    }
}
