<?php

namespace JobMetric\Layout\Services;

use Illuminate\Database\Eloquent\Model;
use JobMetric\Extension\Facades\Plugin;
use JobMetric\Layout\Events\Layout\LayoutDeleteEvent;
use JobMetric\Layout\Events\Layout\LayoutForceDeleteEvent;
use JobMetric\Layout\Events\Layout\LayoutPagesSyncedEvent;
use JobMetric\Layout\Events\Layout\LayoutPluginsSyncedEvent;
use JobMetric\Layout\Events\Layout\LayoutRestoreEvent;
use JobMetric\Layout\Events\Layout\LayoutStoreEvent;
use JobMetric\Layout\Events\Layout\LayoutUpdateEvent;
use JobMetric\Layout\Facades\ApplicationRegistry;
use JobMetric\Layout\Http\Requests\StoreLayoutRequest;
use JobMetric\Layout\Http\Requests\UpdateLayoutRequest;
use JobMetric\Layout\Http\Resources\LayoutResource;
use JobMetric\Layout\Models\Layout as LayoutModel;
use JobMetric\Layout\Models\LayoutPlugin;
use JobMetric\PackageCore\Services\AbstractCrudService;
use Throwable;

/**
 * Class Layout
 *
 * CRUD and management service for Layout entities.
 * Responsibilities:
 * - Validate payloads via StoreLayoutRequest / UpdateLayoutRequest (dto)
 * - Create and sync layout_pages and layout_plugins on store/update
 * - Fire domain events on mutations
 * - Provide getPosition, runPlugins, runPluginsByPosition for resolving and rendering plugins
 * - Expose ordered applications via ApplicationRegistry (getApplications, getApplicationsWithOptions,
 * getApplicationsTranslated)
 *
 * @package JobMetric\Layout
 */
class Layout extends AbstractCrudService
{
    /**
     * Enable soft-deletes + restore/forceDelete APIs.
     *
     * @var bool
     */
    protected bool $softDelete = true;

    /**
     * Enable restore API.
     *
     * @var bool
     */
    protected bool $hasRestore = true;

    /**
     * Enable forceDelete API.
     *
     * @var bool
     */
    protected bool $hasForceDelete = true;

    /**
     * Human-readable entity name key used in response messages.
     *
     * @var string
     */
    protected string $entityName = 'layout::base.entity_names.layout';

    /**
     * Bound model class for the base CRUD.
     *
     * @var class-string<Model>
     */
    protected static string $modelClass = LayoutModel::class;
    protected static string $resourceClass = LayoutResource::class;

    /**
     * Allowed fields for selection/filter/sort in QueryBuilder.
     *
     * @var string[]
     */
    protected static array $fields = [
        'id',
        'name',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Domain events mapping for CRUD lifecycle.
     *
     * @var class-string|null
     */
    protected static ?string $storeEventClass = LayoutStoreEvent::class;
    protected static ?string $updateEventClass = LayoutUpdateEvent::class;
    protected static ?string $deleteEventClass = LayoutDeleteEvent::class;
    protected static ?string $restoreEventClass = LayoutRestoreEvent::class;
    protected static ?string $forceDeleteEventClass = LayoutForceDeleteEvent::class;

    /**
     * Mutate/validate payload before create.
     *
     * Role: ensures a clean, validated input for store().
     *
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldStore(array &$data): void
    {
        $data = dto($data, StoreLayoutRequest::class);
    }

    /**
     * Mutate/validate payload before update.
     *
     * Role: ensures a clean, validated input for update().
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldUpdate(Model $model, array &$data): void
    {
        /** @var LayoutModel $layout */
        $layout = $model;

        $data = dto($data, UpdateLayoutRequest::class, [
            'layout_id' => $layout->id,
        ]);
    }

    /**
     * Hook after create: create layout_pages and layout_plugins from payload.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function afterStore(Model $model, array &$data): void
    {
        /** @var LayoutModel $layout */
        $layout = $model;

        if (! empty($data['pages'])) {
            $layout->layoutPages()->createMany($data['pages']);

            event(new LayoutPagesSyncedEvent($layout, $data['pages']));
        }

        if (! empty($data['plugins'])) {
            $layout->layoutPlugins()->createMany($data['plugins']);

            event(new LayoutPluginsSyncedEvent($layout, $data['plugins']));
        }
    }

    /**
     * Hook after update: sync layout_pages and layout_plugins when provided.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function afterUpdate(Model $model, array &$data): void
    {
        /** @var LayoutModel $layout */
        $layout = $model;

        if (array_key_exists('pages', $data)) {
            $layout->layoutPages()->delete();
            if (! empty($data['pages'])) {
                $layout->layoutPages()->createMany($data['pages']);

                event(new LayoutPagesSyncedEvent($layout, $data['pages']));
            }
        }

        if (array_key_exists('plugins', $data)) {
            $layout->layoutPlugins()->delete();
            if (! empty($data['plugins'])) {
                $layout->layoutPlugins()->createMany($data['plugins']);

                event(new LayoutPluginsSyncedEvent($layout, $data['plugins']));
            }
        }
    }

    /**
     * Get application keys in registry order (for dropdowns, filters, etc.).
     *
     * @return array<int, string>
     */
    public function getApplications(): array
    {
        return ApplicationRegistry::values();
    }

    /**
     * Get all registered applications with options, in registry order.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getApplicationsWithOptions(): array
    {
        return ApplicationRegistry::all();
    }

    /**
     * Get all applications with name and description resolved via trans() for output.
     *
     * @param string|null $locale Locale for trans; null = app locale.
     *
     * @return array<int, array{key: string, name: string, description: string, order: int, options: array<string,
     *                    mixed>}>
     */
    public function getApplicationsTranslated(?string $locale = null): array
    {
        return ApplicationRegistry::allTranslated($locale);
    }

    /**
     * Resolve layout model from id or instance.
     *
     * @param LayoutModel|int $layout
     * @param bool $withTrashed
     *
     * @return LayoutModel|null
     */
    protected function resolveLayout(LayoutModel|int $layout, bool $withTrashed = false): ?LayoutModel
    {
        if ($layout instanceof LayoutModel) {
            return $layout;
        }

        $query = LayoutModel::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($layout);
    }

    /**
     * Get distinct position keys for a layout.
     *
     * @param LayoutModel|int $layout
     *
     * @return array<int, string>
     */
    public function getPosition(LayoutModel|int $layout): array
    {
        $layout = $this->resolveLayout($layout);

        if ($layout === null) {
            return [];
        }

        $positions = $layout->layoutPlugins->pluck('position')->unique()->values();

        return $positions->all();
    }

    /**
     * Render all plugins for a layout, grouped by position.
     *
     * @param LayoutModel|int $layout
     *
     * @return array<string, array<int, mixed>>
     * @throws Throwable
     */
    public function runPlugins(LayoutModel|int $layout): array
    {
        $layout = $this->resolveLayout($layout);

        if ($layout === null) {
            return [];
        }

        $positions = $this->getPosition($layout);
        $plugins = [];

        foreach ($positions as $position) {
            $layoutPlugins = LayoutPlugin::query()
                ->where('layout_id', $layout->id)
                ->where('position', $position)
                ->orderBy('ordering')
                ->get();

            foreach ($layoutPlugins as $layoutPlugin) {
                $plugins[$layoutPlugin->position][] = Plugin::run($layoutPlugin->plugin_id);
            }
        }

        return $plugins;
    }

    /**
     * Render plugins for a layout at a single position.
     *
     * @param LayoutModel|int $layout
     * @param string $position
     *
     * @return array<int, mixed>
     * @throws Throwable
     */
    public function runPluginsByPosition(LayoutModel|int $layout, string $position): array
    {
        $layout = $this->resolveLayout($layout);

        if ($layout === null) {
            return [];
        }

        $layoutPlugins = LayoutPlugin::query()
            ->where('layout_id', $layout->id)
            ->where('position', $position)
            ->orderBy('ordering')
            ->get();

        $plugins = [];
        foreach ($layoutPlugins as $layoutPlugin) {
            $plugins[] = Plugin::run($layoutPlugin->plugin_id);
        }

        return $plugins;
    }
}
