<?php

namespace Sorane\Lemme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sorane\Lemme\Lemme
 */
class Lemme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sorane\Lemme\Lemme::class;
    }
}
