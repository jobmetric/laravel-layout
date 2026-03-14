<?php

namespace JobMetric\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Layout\Support\ApplicationRegistry
 *
 * @method static \JobMetric\Layout\Support\ApplicationRegistry register(string $key, array<string, mixed> $options = [])
 * @method static \JobMetric\Layout\Support\ApplicationRegistry unregister(string $key)
 * @method static bool has(string $key)
 * @method static array<string, mixed>|null get(string $key)
 * @method static mixed getOption(string $key, string $option, mixed $default = null)
 * @method static array<string, array<string, mixed> > all()
 * @method static array<int, array<string, mixed> > allTranslated(string | null $locale = null)
 * @method static array<int, string> values()
 * @method static \JobMetric\Layout\Support\ApplicationRegistry clear()
 */
class ApplicationRegistry extends Facade
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
        return 'ApplicationRegistry';
    }
}
