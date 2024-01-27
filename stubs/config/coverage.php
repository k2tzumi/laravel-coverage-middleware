<?php
declare(strict_types=1);

return [
    /*
    |---------------------------------------------------------------------------
    | PHPUnit Configure file path
    |---------------------------------------------------------------------------
    |
    | Refer to the coverage settings in the PHPUnit configuration file. 
    | Please specify the path to the configuration file
    |
    */
    'phpunit_config_path' => base_path('phpunit.xml'),

    /*
    |---------------------------------------------------------------------------
    | runn trace header
    |---------------------------------------------------------------------------
    |
    | Specify the header name of the runn trace.
    | see https://github.com/k1LoW/runn?tab=readme-ov-file#trace
    |
    */
    'runn-trace-header' => 'X-Runn-Trace',
];