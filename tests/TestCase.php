<?php
declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage::class
        ];
    }
}