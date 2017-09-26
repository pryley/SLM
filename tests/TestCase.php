<?php

namespace Tests;

use App\Exceptions\Handler;
use App\User;
use Illuminate\Contracts\Debug\ExceptionHandler;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use CreatesApplication;

    protected function disableExceptionHandling()
    {
        $this->app->instance( ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report( \Exception $e ) {}
            public function render( $request, \Exception $e ) {
                throw $e;
            }
        });
    }

    protected function auth()
    {
        return $this->actingAs( factory( User::class )->create(), 'api' );
    }
}
