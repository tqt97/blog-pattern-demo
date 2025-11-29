<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BindRepositoryCommand extends Command
{
    protected $signature = 'make:bind-repository
        {domain : Domain name, e.g. Category, Tag, Post}';

    protected $description = 'Add repository binding for a domain in RepositoryServiceProvider';

    public function handle(): int
    {
        $domain = ucfirst($this->argument('domain'));

        $this->updateRepositoryServiceProvider($domain);

        return self::SUCCESS;
    }

    protected function updateRepositoryServiceProvider(string $domain): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (! File::exists($providerPath)) {
            $this->error('RepositoryServiceProvider not found.');

            return;
        }

        $content = File::get($providerPath);

        $interfaceUse = "use App\\Repositories\\Contracts\\{$domain}RepositoryInterface;";
        $repositoryUse = "use App\\Repositories\\Eloquent\\{$domain}Repository;";
        $bindLine = "\$this->app->bind({$domain}RepositoryInterface::class, {$domain}Repository::class);";

        // Thêm use Interface
        if (! str_contains($content, $interfaceUse)) {
            $content = $this->insertUseStatement($content, $interfaceUse);
            $this->info("Added use: {$interfaceUse}");
        } else {
            $this->warn("Use for {$domain}RepositoryInterface already exists, skipped.");
        }

        // Thêm use Repository
        if (! str_contains($content, $repositoryUse)) {
            $content = $this->insertUseStatement($content, $repositoryUse);
            $this->info("Added use: {$repositoryUse}");
        } else {
            $this->warn("Use for {$domain}Repository already exists, skipped.");
        }

        // Thêm bind line trong boot()
        if (! str_contains($content, $bindLine)) {
            $content = $this->insertBindInBoot($content, $bindLine);
            $this->info("Added binding: {$bindLine}");
        } else {
            $this->warn("Binding for {$domain}RepositoryInterface already exists, skipped.");
        }

        File::put($providerPath, $content);
    }

    /**
     * Chèn dòng use mới sau các use hiện có (sau ServiceProvider nếu tìm thấy).
     */
    protected function insertUseStatement(string $content, string $useLine): string
    {
        $needle = 'use Illuminate\\Support\\ServiceProvider;';

        if (str_contains($content, $needle)) {
            return str_replace(
                $needle,
                $needle.PHP_EOL.$useLine,
                $content
            );
        }

        // Fallback: chèn sau namespace
        $pattern = '/namespace App\\\\Providers;\\s*/';
        if (preg_match($pattern, $content, $matches)) {
            $replacement = $matches[0].PHP_EOL.$useLine.PHP_EOL;

            return preg_replace($pattern, $replacement, $content, 1);
        }

        // Nếu không detect được, append cuối file (ít khi xảy ra)
        return $content.PHP_EOL.$useLine.PHP_EOL;
    }

    /**
     * Chèn bind line vào trong phương thức boot().
     */
    protected function insertBindInBoot(string $content, string $bindLine): string
    {
        $pattern = '/public function boot\\(\\): void\\s*\\{(.*?)\\n\\s*\\}/s';

        if (preg_match($pattern, $content, $matches)) {
            $body = rtrim($matches[1]).PHP_EOL.'        '.$bindLine.PHP_EOL;

            $replacement = "public function boot(): void\n    {\n".$body.'    }';

            return preg_replace($pattern, $replacement, $content, 1);
        }

        // fallback: nhét ngay sau dòng mở ngoặc của boot()
        $fallback = 'public function boot(): void';
        if (str_contains($content, $fallback)) {
            $pos = strpos($content, $fallback);
            $bracePos = strpos($content, '{', $pos);
            if ($bracePos !== false) {
                $insertPos = strpos($content, PHP_EOL, $bracePos) + 1;
                $bindString = '        '.$bindLine.PHP_EOL;

                return substr($content, 0, $insertPos)
                    .$bindString
                    .substr($content, $insertPos);
            }
        }

        // Nếu vẫn fail, append cuối file (cực kỳ ít khi cần)
        return $content.PHP_EOL.'        '.$bindLine.PHP_EOL;
    }
}
