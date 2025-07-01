<?php
declare(strict_types=1);

namespace K2tzumi\LaravelCoverageMiddleware\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPUnit\TextUI\Configuration\SourceMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use function is_array;

/**
 * Middleware to output code coverage
 * @package K2tzumi\LaravelCoverageMiddleware\Http\Middleware
 */
class CollectCodeCoverage
{
    /**
     * @var CodeCoverage
     */
    private readonly CodeCoverage $coverage;

    /**
     * @var Filter
     */
    private static Filter $filter;

    const DEFALUT_PHPUNIT_CONFIG = 'phpunit.xml';

    const DEFALUT_HEADER_NAME = 'X-Runn-Trace';

    /**
     * @throws NoCodeCoverageDriverAvailableException
     */
    public function __construct()
    {
        if (!isset(self::$filter)) {
            $loader = new Loader;
            $configPath = config('coverage.phpunit_config_path', self::DEFALUT_PHPUNIT_CONFIG);
            $configuration = $loader->load(is_string($configPath) ? $configPath : self::DEFALUT_PHPUNIT_CONFIG);
            self::$filter = new Filter;
            self::$filter->includeFiles(array_keys((new SourceMapper)->map($configuration->source())));
        }

        $this->coverage = new CodeCoverage(
            (new Selector)->forLineCoverage(self::$filter),
            self::$filter
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $headerName = config('coverage.runn-trace-header', self::DEFALUT_HEADER_NAME);
        $trace = $request->header(is_string($headerName) ? $headerName : self::DEFALUT_HEADER_NAME);
        if (empty($trace) || is_array($trace)) {
            return $next($request);
        }
        $json = json_decode($trace, true);
        if (is_array($json) === false) {
            return $next($request);
        }
        /** @var string $requestId */
        $requestId = $json['id'] ?? '';
        $runbookId = strstr($requestId, '?', true);
        if ($runbookId === false) {
            return  $next($request);
        }

        $this->coverage->start($requestId);
        $response = $next($request);
        $this->coverage->stop();

        // Save coverage data
        (new PHP())->process($this->coverage, storage_path("coverage/$runbookId-" . microtime(true)) . '.cov');

        return $response;
    }
}
