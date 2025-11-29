<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFilterCommand extends Command
{
    protected $signature = 'make:filter
        {domain : Domain name (folder under App\\Filters\\Eloquent\\Domains)}';

    protected $description = 'Generate an Eloquent Filter under App\\Filters\\Eloquent\\Domains';

    public function handle()
    {
        $domain = ucfirst($this->argument('domain'));
        $className = "{$domain}Filter";

        $directory = app_path("Filters/Eloquent/Domains/{$domain}");
        $filePath = "{$directory}/{$className}.php";

        // Create directory if needed
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Check if exists
        if (File::exists($filePath)) {
            $this->error("Filter {$className} already exists in domain {$domain}.");

            return Command::FAILURE;
        }

        // Content
        $stub = <<<PHP
        <?php

        namespace App\Filters\Eloquent\Domains\\{$domain};

        use App\Filters\Eloquent\Base\EloquentFilters;

        class {$className} extends EloquentFilters
        {
            protected array \$searchable = [];

            protected array \$sortable = [];
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Filter created: App\\Filters\\Eloquent\\Domains\\{$domain}\\{$className}");

        return Command::SUCCESS;
    }
}
