<?php

namespace JobMetric\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Layout\Layout
 *
 * @method static array store(array $data)
 * @method static array update(int $layout_id, array $data)
 * @method static array delete(int $layout_id)
 */
class Layout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Layout';
    }
}
