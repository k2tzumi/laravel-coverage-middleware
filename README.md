![test passsed](https://github.com/k2tzumi/laravel-coverage-middleware/actions/workflows/test.yml/badge.svg?branch=main)  

# laravel-coverage-middleware
HTTP middleware to get code coverage of Laravel applications on remote servers

## Introduction

The laravel-coverage-middleware library is designed to help developers obtain code coverage when performing API tests on remote Laravel applications. The library provides a middleware that can be used to collect code coverage data during API tests, which can then be used to generate reports and identify areas of the codebase that require further testing.

## Requirements

 Here are the environment requirements for laravel-coverage-middleware:

* [PHP](https://www.php.net/)  
Version 8.2 or higher.
* [Laravel](https://laravel.com/)  
Version 9.0 or higher.
* [PHPUnit](https://github.com/sebastianbergmann/phpunit)  
Version 9.0 or higher.
* [runn](https://github.com/k1LoW/runn)  
Version 0.93.0 or higher.

## Installation

Here are the steps to install laravel-coverage-middleware:

1. Run the following command to install the library as a development dependency  
  ```console
  composer require --dev k2tzumi/laravel-coverage-middleware
  ```

2. Run the following command to publish the configuration file of the library:  
  ```console
  php artisan vendor:publish --provider="K2tzumi\LaravelCoverageMiddleware\Providers\CoverageServiceProvider"
  ```

3. Run the following command to install the coverage middleware:  
  ```console
  php artisan coverage:install {group}
  ```
Please replace `group` with the name of the middleware group that you want to include in the coverage report.

## Usage

Here are the steps to use laravel-coverage-middleware:

1. Once the library is installed, start the Laravel application, enable httpRunner tracing in [runn](https://github.com/k1LoW/runn), and run the API test. 
2. The coverage data will be collected during the API tests and stored in the storage/coverage directory.  
3. You can use phpcov to generate HTML reports from the coverage files and view them to identify areas of the codebase that require further testing.

## Configuration

After installing laravel-coverage-middleware, you can edit the config/coverage.php file to configure the library. The following are the available configuration options:  

* phpunit_config_path(default: `phpunit.xml`)  
Specifies the path to the PHPUnit configuration file. The coverage data collected by the library will be based on the source settings in this configuration file.
* runn-trace-header(default: `X-Runn-Trace`)  
Specifies the trace header for httpRunner. If you are not using runn, you can specify the name of the request header that contains the trace information for your tool.

