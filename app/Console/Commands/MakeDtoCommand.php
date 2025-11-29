<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDtoCommand extends Command
{
    protected $signature = 'make:dto
        {domain : Domain name (will also be used as class name)}';

    protected $description = 'Generate DTO and FilterDTO classes under App\\DTOs\\Domains';

    public function handle()
    {
        $domain = ucfirst($this->argument('domain'));

        $directory = app_path("DTOs/Domains/{$domain}");

        // Ensure directory exists
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $this->createMainDto($domain, $directory);
        $this->createFilterDto($domain, $directory);

        return Command::SUCCESS;
    }

    protected function createMainDto(string $domain, string $directory): void
    {
        $className = "{$domain}DTO";
        $filePath = "{$directory}/{$className}.php";

        if (File::exists($filePath)) {
            $this->warn("DTO {$className} already exists for domain {$domain}, skipped.");

            return;
        }

        $stub = <<<PHP
        <?php

        namespace App\DTOs\Domains\\{$domain};

        use App\DTOs\BaseDTO;

        class {$className} extends BaseDTO
        {
            public function __construct()
            {
            }
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Created: App\\DTOs\\Domains\\{$domain}\\{$className}");
    }

    protected function createFilterDto(string $domain, string $directory): void
    {
        $className = "{$domain}FilterDTO";
        $filePath = "{$directory}/{$className}.php";

        if (File::exists($filePath)) {
            $this->warn("DTO {$className} already exists for domain {$domain}, skipped.");

            return;
        }

        $stub = <<<PHP
        <?php

        namespace App\DTOs\Domains\\{$domain};

        use App\DTOs\BaseDTO;

        class {$className} extends BaseDTO
        {
            public function __construct(
                public readonly ?int \$perPage,
            ) {}
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Created: App\\DTOs\\Domains\\{$domain}\\{$className}");
    }
}
