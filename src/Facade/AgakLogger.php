<?php

namespace Mhafizhasan\AgakCore\Facade;

use Illuminate\Support\Facades\Facade;

/**
 *
 */
class AgakLogger extends Facade
{
    protected static function getFacadeAccessor() {
        return 'AgakLogger';
    }
}
