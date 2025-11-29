<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository
        {domain : Domain name, e.g. Tag, Category, Post}';

    protected $description = 'Generate Repository interface and Eloquent implementation for a given domain';

    public function handle()
    {
        $domain = ucfirst($this->argument('domain'));

        $this->createInterface($domain);
        $this->createRepository($domain);

        $this->info("Repository for domain [{$domain}] created successfully.");

        return self::SUCCESS;
    }

    protected function createInterface(string $domain): void
    {
        $interfaceName = "{$domain}RepositoryInterface";
        $directory = app_path('Repositories/Contracts');
        $filePath = "{$directory}/{$interfaceName}.php";

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->warn("Interface {$interfaceName} already exists, skipped.");

            return;
        }

        $stub = <<<PHP
        <?php

        namespace App\Repositories\Contracts;

        use App\DTOs\Domains\\{$domain}\\{$domain}FilterDTO;
        use Illuminate\Contracts\Pagination\LengthAwarePaginator;

        interface {$interfaceName} extends BaseRepositoryInterface
        {
            public function paginate({$domain}FilterDTO \$filter): LengthAwarePaginator;
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Created: App\\Repositories\\Contracts\\{$interfaceName}");
    }

    protected function createRepository(string $domain): void
    {
        $className = "{$domain}Repository";
        $interfaceName = "{$domain}RepositoryInterface";

        $directory = app_path('Repositories/Eloquent');
        $filePath = "{$directory}/{$className}.php";

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->warn("Repository {$className} already exists, skipped.");

            return;
        }

        $stub = <<<PHP
        <?php

        namespace App\Repositories\Eloquent;

        use App\DTOs\Domains\\{$domain}\\{$domain}FilterDTO;
        use App\Filters\Eloquent\Domains\\{$domain}\\{$domain}Filter;
        use App\Models\\{$domain};
        use App\Repositories\Concerns\FilterableRepository;
        use App\Repositories\Contracts\\{$interfaceName};
        use Illuminate\Contracts\Pagination\LengthAwarePaginator;

        class {$className} extends BaseRepository implements {$interfaceName}
        {
            use FilterableRepository;

            public function __construct({$domain} \$model)
            {
                parent::__construct(\$model);
            }

            public function filterClass(): string
            {
                return {$domain}Filter::class;
            }

            public function paginate({$domain}FilterDTO \$filter): LengthAwarePaginator
            {
                \$query = \$this->query();
                \$query = \$this->applyFilters(\$filter);

                return \$query->paginate(\$perPage);
            }
        }

        PHP;

        File::put($filePath, $stub);

        $this->info("Created: App\\Repositories\\Eloquent\\{$className}");
    }
}
