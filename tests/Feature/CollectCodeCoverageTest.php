<?php
declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage;
use Tests\TestCase;

/**
 * @package Tests\Feature
 * @coversDefaultClass \K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage
 * @covers \K2tzumi\LaravelCoverageMiddleware\Http\Middleware\CollectCodeCoverage
 */
class CollectCodeCoverageTest extends TestCase
{
    /**
     * @test
     * @covers ::handle
     * @return void
     */
    public function testHandleWithOutHeader(): void
    {
        setup:
        $instance = $this->createInstance();

        // Create requests and closures
        $request = Request::create('/test', 'GET');
        $next = function ($request) {
            return new Response('OK');
        };

        when:
        $response = $instance->handle($request, $next);

        then:
        $this->assertEquals('OK', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return CollectCodeCoverage
     */
    private function createInstance() : CollectCodeCoverage {
        return app(CollectCodeCoverage::class);
    }
}
