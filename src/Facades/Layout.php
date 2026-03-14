<?php

namespace JobMetric\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Layout\Services\Layout
 *
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filters = [], array $with = [], string | null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response paginate(int $pageLimit = 15, array $filters = [], array $with = [], string | null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response all(array $filters = [], array $with = [], string | null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response show(int $id, array $with = [], string | null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response store(array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response update(int $id, array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response destroy(int $id, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response restore(int $id, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response forceDelete(int $id, array $with = [])
 * @method static array<int, string> getApplications()
 * @method static array<string, array<string, mixed> > getApplicationsWithOptions()
 * @method static array<int, array<string, mixed> > getApplicationsTranslated(string | null $locale = null)
 * @method static array<int, string> getPosition(\JobMetric\Layout\Models\Layout | int $layout)
 * @method static array<string, array<int, mixed> > runPlugins(\JobMetric\Layout\Models\Layout | int $layout)
 * @method static array<int, mixed> runPluginsByPosition(\JobMetric\Layout\Models\Layout | int $layout, string $position)
 */
class Layout extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     *
     * This accessor must match the binding defined in the package service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Layout';
    }
}
