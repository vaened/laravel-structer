<?php
/**
 * Created by enea dhack - 30/05/2017 04:42 PM.
 */

namespace Vaened\Structer\Tests;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config($this->app->make('config'));
    }

    protected function config(ConfigContract $config): void
    {
        $config->set('laravel-structer.allow-mass-assignment', true);
    }

    protected function turnOffMassAssignment(): void
    {
        config()->set('laravel-structer.allow-mass-assignment', false);
    }

    protected function turnOnMassAssignment(): void
    {
        config()->set('laravel-structer.allow-mass-assignment', true);
    }
}
