<?php

namespace JobMetric\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Layout\Layout
 *
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [], string $mode = 'withTrashed')
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = 'withTrashed')
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [], string $mode = 'withTrashed')
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
