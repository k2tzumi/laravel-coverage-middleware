<?php
declare(strict_types=1);

namespace K2tzumi\LaravelCoverageMiddleware\Providers;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage;
use K2tzumi\LaravelCoverageMiddleware\Providers\Console\InstallCommand;

/**
 * This provider will publish the necessary files in the specified directories.
 * @package K2tzumi\LaravelCoverageMiddleware\Providers
 */
class CoverageServiceProvider extends ServiceProvider
{
    const STUB_DIR = __DIR__.'/../../stubs';

    /**
     * @inheritDoc
     */
    public function boot(Router $router): void
    {
        Log::info('CoverageServiceProvider boot method called.');
        // Register the middleware globally
        /** @var Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(CollectCodeCoverage::class);

        // Publishing is only necessary when using the CLI
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            self::STUB_DIR.'/config/coverage.php' => config_path('coverage.php'),
        ], 'coverage-config');
        // Setup command
        $this->commands([
            InstallCommand::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            self::STUB_DIR.'/config/coverage.php',
            'coverage-config'
        );
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [Console\InstallCommand::class];
    }
}
