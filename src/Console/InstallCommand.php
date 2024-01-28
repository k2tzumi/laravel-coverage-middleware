<?php
declare(strict_types=1);

namespace K2tzumi\LaravelCoverageMiddleware\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage;

/**
 * @packages K2tzumi\LaravelCoverageMiddleware\Console
 */
class InstallCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coveage:install {group: Specify the middleware group from which to obtain coverage}';

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
            $group = $this->prompt('Please specify the middleware group from which to obtain coverage');
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

        $middlewareGroups = Str::before(Str::after($kernelContents, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (!Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = $middlewareGroup . '    \\'.$name.'::class,'.PHP_EOL.'        ';

            file_put_contents($kernelPath, str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $kernelContents
            ));

            $this->line("Middleware {$name} added to {$group} middleware group");
        }
    }
}
