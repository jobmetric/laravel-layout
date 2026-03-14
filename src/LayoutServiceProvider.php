<?php

namespace JobMetric\Layout;

use Illuminate\Contracts\Container\BindingResolutionException;
use JobMetric\EventSystem\Support\EventRegistry;
use JobMetric\Layout\Services\Layout;
use JobMetric\Layout\Support\ApplicationRegistry;
use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;

class LayoutServiceProvider extends PackageCoreServiceProvider
{
    /**
     * @param PackageCore $package
     *
     * @return void
     * @throws MigrationFolderNotFoundException
     * @throws RegisterClassTypeNotFoundException
     */
    public function configuration(PackageCore $package): void
    {
        $package->name('laravel-layout')
            ->hasConfig()
            ->hasTranslation()
            ->hasMigration()
            ->registerClass('Layout', Layout::class)
            ->registerClass('ApplicationRegistry', ApplicationRegistry::class, RegisterClassTypeEnum::SINGLETON());
    }

    /**
     * After boot package: register domain events when EventRegistry is available.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function afterBootPackage(): void
    {
        if ($this->app->bound('EventRegistry')) {
            /** @var EventRegistry $registry */
            $registry = $this->app->make('EventRegistry');

            $registry->register(\JobMetric\Layout\Events\Layout\LayoutStoreEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutUpdateEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutDeleteEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutRestoreEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutForceDeleteEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutPagesSyncedEvent::class);
            $registry->register(\JobMetric\Layout\Events\Layout\LayoutPluginsSyncedEvent::class);
            $registry->register(\JobMetric\Layout\Events\HasLayout\LayoutRelationStoredEvent::class);
            $registry->register(\JobMetric\Layout\Events\HasLayout\LayoutRelationDeletedEvent::class);
        }
    }
}
