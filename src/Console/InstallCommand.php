<?php
declare(strict_types=1);

namespace K2tzumi\LaravelCoverageMiddleware\Providers\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage;

/**
 * @packages K2tzumi\LaravelCoverageMiddleware\Providers
 */
class InstallCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coveage:install {group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the coverage middleware and resources';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $group = $this->argument('group');
        if ($group === null || is_string($group) !== true) {
            $this->error('The group is required.');
            return Command::FAILURE;
        }

        // Middleware...
        $this->installMiddlewareLast(CollectCodeCoverage::class, strval($group));

        return Command::SUCCESS;
    }


    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $name
     * @param  string  $group
     * @return void
     * @throws FileNotFoundException
     */
    protected function installMiddlewareLast(string $name, string $group): void
    {
        $kernelPath = app_path('Http/Kernel.php');
        $kernelContents = file_get_contents($kernelPath);
        if ($kernelContents === false) {
            throw new FileNotFoundException('Http/Kernel not found');
        }

        $middlewareGroupRegex = "/protected\s+\${$group}Middleware\s*=\s*\[(.*?)\]/s";
        preg_match($middlewareGroupRegex, $kernelContents, $middlewareGroupMatch);

        if (empty($middlewareGroupMatch)) {
            $this->line("Middleware group {$group} not found");
            return;
        }

        $middlewareGroupContents = $middlewareGroupMatch[1];

        $middlewareList = preg_split("/\r\n|\n|\r/", $middlewareGroupContents);
        if ($middlewareList === false) {
            throw new \UnexpectedValueException("Http/Kernel parsing failed. middlewareGroupContents: {$middlewareGroupContents}");
        }
        $middlewareList = array_map('trim', $middlewareList);
        $middlewareList = array_filter($middlewareList);

        if (in_array($name, $middlewareList)) {
            $this->line("Middleware {$name} already exists in {$group} middleware group");
            return;
        }

        $middleware_list[] = $name;

        $middlewareGroupContents = implode("\n", $middlewareList);

        $kernelContents = preg_replace($middlewareGroupRegex, "protected \${$group}Middleware = [{$middlewareGroupContents}]", $kernelContents);

        file_put_contents($kernelPath, $kernelContents);

        $this->line("Middleware {$name} added to {$group} middleware group");
    }
}
