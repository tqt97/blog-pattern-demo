<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service-domain
        {domain : Domain name, e.g. Category, Tag, Post}';

    protected $description = 'Generate a Service class for a given domain';

    public function handle(): int
    {
        $domain = ucfirst($this->argument('domain'));

        $this->createService($domain);

        return self::SUCCESS;
    }

    protected function createService(string $domain): void
    {
        $className = "{$domain}Service";
        $directory = app_path('Services');
        $filePath = "{$directory}/{$className}.php";

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->warn("Service {$className} already exists, skipped.");

            return;
        }

        $propertyVar = lcfirst($domain);

        $stub = <<<PHP
        <?php

        namespace App\Services;

        use App\DTOs\Domains\\{$domain}\\{$domain}DTO;
        use App\DTOs\Domains\\{$domain}\\{$domain}FilterDTO;
        use App\Models\\{$domain};
        use App\Repositories\Contracts\\{$domain}RepositoryInterface;
        use Illuminate\Contracts\Pagination\LengthAwarePaginator;

        class {$className}
        {
            public function __construct(
                protected {$domain}RepositoryInterface \${$propertyVar}Repository,
            ) {}

            public function list({$domain}FilterDTO \$filter): LengthAwarePaginator
            {
                return \$this->{$propertyVar}Repository->paginate(\$filter);
            }

            public function create({$domain}DTO \$dto): {$domain}
            {
                return \$this->{$propertyVar}Repository->create(\$dto->toArray());
            }

            public function update(int \$id, {$domain}DTO \$dto): {$domain}
            {
                return \$this->{$propertyVar}Repository->update(\$id, \$dto->toArray());
            }

            public function delete(int \$id): void
            {
                \$this->{$propertyVar}Repository->delete(\$id);
            }
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Created: App\\Services\\{$className}");
    }
}
