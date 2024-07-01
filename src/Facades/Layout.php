<?php

namespace JobMetric\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Layout\Layout
 *
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [], string $mode = null)
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = null)
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [], string $mode = null)
 * @method static array get(int $layout_id, array $with = [], string $mode = null)
 * @method static array store(array $data)
 * @method static array update(int $layout_id, array $data = [])
 * @method static array delete(int $layout_id)
 * @method static array restore(int $layout_id)
 * @method static array forceDelete(int $layout_id)
 * @method static array getPosition(\JobMetric\Layout\Models\Layout|int $layout)
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
        return \JobMetric\Layout\Layout::class;
    }
}
