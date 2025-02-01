<?php

namespace JobMetric\Layout;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JobMetric\Extension\Facades\Plugin;
use JobMetric\Layout\Events\LayoutDeleteEvent;
use JobMetric\Layout\Events\LayoutForceDeleteEvent;
use JobMetric\Layout\Events\LayoutRestoreEvent;
use JobMetric\Layout\Events\LayoutStoreEvent;
use JobMetric\Layout\Events\LayoutUpdateEvent;
use JobMetric\Layout\Http\Requests\StoreLayoutRequest;
use JobMetric\Layout\Http\Requests\UpdateLayoutRequest;
use JobMetric\Layout\Http\Resources\LayoutResource;
use JobMetric\Layout\Models\Layout as LayoutModel;
use JobMetric\Layout\Models\LayoutPlugin;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class Layout
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new Setting instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the specified layout.
     *
     * @param array $filter
     * @param array $with
     * @param string|null $mode
     *
     * @return QueryBuilder
     */
    private function query(array $filter = [], array $with = [], string $mode = null): QueryBuilder
    {
        $fields = ['id', 'name', 'status', 'created_at', 'updated_at'];

        $query = QueryBuilder::for(LayoutModel::class);

        if ($mode === 'withTrashed') {
            $query->withTrashed();
        }

        if ($mode === 'onlyTrashed') {
            $query->onlyTrashed();
        }

        $query->allowedFields($fields)
            ->allowedSorts($fields)
            ->allowedFilters($fields)
            ->defaultSort([
                '-id'
            ])
            ->where($filter);

        if (!empty($with)) {
            $query->with($with);
        }

        return $query;
    }

    /**
     * Paginate the specified layout.
     *
     * @param array $filter
     * @param int $page_limit
     * @param array $with
     * @param string|null $mode
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = null): AnonymousResourceCollection
    {
        return LayoutResource::collection(
            $this->query($filter, $with, $mode)->paginate($page_limit)
        );
    }

    /**
     * Get all layouts.
     *
     * @param array $filter
     * @param array $with
     * @param string|null $mode
     *
     * @return AnonymousResourceCollection
     */
    public function all(array $filter = [], array $with = [], string $mode = null): AnonymousResourceCollection
    {
        return LayoutResource::collection(
            $this->query($filter, $with, $mode)->get()
        );
    }

    /**
     * Get the specified layout.
     *
     * @param int $layout_id
     * @param array $with
     * @param string|null $mode
     *
     * @return array
     */
    public function get(int $layout_id, array $with = [], string $mode = null): array
    {
        if ($mode === 'withTrashed') {
            $query = LayoutModel::withTrashed();
        } else if ($mode === 'onlyTrashed') {
            $query = LayoutModel::onlyTrashed();
        } else {
            $query = LayoutModel::query();
        }

        $query->where('id', $layout_id);

        if (!empty($with)) {
            if(isset($with['layoutPlugins'])) {
                $with['layoutPlugins'] = function ($query) {
                    $query->orderBy('ordering');
                };
            }

            $query->with($with);
        }

        $layout = $query->first();

        if (!$layout) {
            return [
                'ok' => false,
                'message' => trans('package-core::base.validation.errors'),
                'errors' => [
                    trans('layout::base.validation.object_not_found')
                ],
                'status' => 404
            ];
        }

        return [
            'ok' => true,
            'message' => trans('layout::base.messages.found'),
            'data' => LayoutResource::make($layout),
            'status' => 200
        ];
    }

    /**
     * Store the specified layout.
     *
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function store(array $data): array
    {
        $validator = Validator::make($data, (new StoreLayoutRequest)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('package-core::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();

            return DB::transaction(function () use ($data) {
                $layout = new LayoutModel;
                $layout->name = $data['name'];
                $layout->status = $data['status'];

                $layout->save();

                $layout->layoutPages()->createMany($data['pages']);
                $layout->layoutPlugins()->createMany($data['plugins']);

                event(new LayoutStoreEvent($layout, $data));

                return [
                    'ok' => true,
                    'message' => trans('layout::base.messages.created'),
                    'data' => LayoutResource::make($layout),
                    'status' => 201
                ];
            });
        }
    }

    /**
     * Update the specified layout.
     *
     * @param int $layout_id
     * @param array $data
     *
     * @return array
     */
    public function update(int $layout_id, array $data = []): array
    {
        $validator = Validator::make($data, (new UpdateLayoutRequest)->setLayoutId($layout_id)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('package-core::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();

            return DB::transaction(function () use ($layout_id, $data) {
                /**
                 * @var LayoutModel $layout
                 */
                $layout = LayoutModel::query()->where('id', $layout_id)->first();

                if (!$layout) {
                    return [
                        'ok' => false,
                        'message' => trans('package-core::base.validation.errors'),
                        'errors' => [
                            trans('layout::base.validation.object_not_found')
                        ],
                        'status' => 404
                    ];
                }

                if (array_key_exists('name', $data)) {
                    $layout->name = $data['name'];
                }

                if (array_key_exists('status', $data)) {
                    $layout->status = $data['status'];
                }

                $layout->save();

                if (array_key_exists('pages', $data)) {
                    $layout->layoutPages()->delete();
                    $layout->layoutPages()->createMany($data['pages']);
                }

                if (array_key_exists('plugins', $data)) {
                    $layout->layoutPlugins()->delete();
                    $layout->layoutPlugins()->createMany($data['plugins']);
                }

                event(new LayoutUpdateEvent($layout, $data));

                return [
                    'ok' => true,
                    'message' => trans('layout::base.messages.updated'),
                    'data' => LayoutResource::make($layout),
                    'status' => 200
                ];
            });
        }
    }

    /**
     * Delete the specified layout and send to trash.
     *
     * @param int $layout_id
     *
     * @return array
     */
    public function delete(int $layout_id): array
    {
        return DB::transaction(function () use ($layout_id) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::query()->where('id', $layout_id)->first();

            if (!$layout) {
                return [
                    'ok' => false,
                    'message' => trans('package-core::base.validation.errors'),
                    'errors' => [
                        trans('layout::base.validation.object_not_found')
                    ],
                    'status' => 404
                ];
            }

            event(new LayoutDeleteEvent($layout));

            $data = LayoutResource::make($layout);

            $layout->delete();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('layout::base.messages.deleted'),
                'status' => 200
            ];
        });
    }

    /**
     * Restore the specified layout.
     *
     * @param int $layout_id
     *
     * @return array
     */
    public function restore(int $layout_id): array
    {
        return DB::transaction(function () use ($layout_id) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::onlyTrashed()->where('id', $layout_id)->first();

            if (!$layout) {
                return [
                    'ok' => false,
                    'message' => trans('package-core::base.validation.errors'),
                    'errors' => [
                        trans('layout::base.validation.object_not_found')
                    ],
                    'status' => 404
                ];
            }

            event(new LayoutRestoreEvent($layout));

            $data = LayoutResource::make($layout);

            $layout->restore();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('layout::base.messages.restored'),
                'status' => 200
            ];
        });
    }

    /**
     * Force delete the specified layout.
     *
     * @param int $layout_id
     *
     * @return array
     */
    public function forceDelete(int $layout_id): array
    {
        return DB::transaction(function () use ($layout_id) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::onlyTrashed()->where('id', $layout_id)->first();

            if (!$layout) {
                return [
                    'ok' => false,
                    'message' => trans('package-core::base.validation.errors'),
                    'errors' => [
                        trans('layout::base.validation.object_not_found')
                    ],
                    'status' => 404
                ];
            }

            event(new LayoutForceDeleteEvent($layout));

            $data = LayoutResource::make($layout);

            $layout->forceDelete();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('layout::base.messages.permanently_deleted'),
                'status' => 200
            ];
        });
    }

    /**
     * Get position
     *
     * @param LayoutModel|int $layout
     *
     * @return array
     */
    public function getPosition(LayoutModel|int $layout): array
    {
        if (!$layout instanceof LayoutModel) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::query()->where('id', $layout)->first();
        }

        if (!$layout) {
            return [];
        }

        $position = $layout->layoutPlugins->map(function ($item) {
            return $item->position;
        })->toArray();

        return array_unique($position);
    }

    /**
     * Render plugins
     *
     * @param LayoutModel|int $layout
     *
     * @return array
     */
    public function runPlugins(LayoutModel|int $layout): array
    {
        if (!$layout instanceof LayoutModel) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::query()->where('id', $layout)->first();
        }

        $positions = $this->getPosition($layout);

        $plugins = [];
        foreach ($positions as $position) {
            $layout_plugins = LayoutPlugin::query()->where([
                'layout_id' => $layout->id,
                'position' => $position
            ])->orderBy('ordering')->get();

            foreach ($layout_plugins as $layout_plugin) {
                $plugins[$layout_plugin->position][] = Plugin::run($layout_plugin->plugin_id);
            }
        }

        return $plugins;
    }

    /**
     * Render plugins in the specified position.
     *
     * @param LayoutModel|int $layout
     * @param string $position
     *
     * @return array
     */
    public function runPluginsByPosition(LayoutModel|int $layout, string $position): array
    {
        if (!$layout instanceof LayoutModel) {
            /**
             * @var LayoutModel $layout
             */
            $layout = LayoutModel::query()->where('id', $layout)->first();
        }

        $layout_plugins = LayoutPlugin::query()->where([
            'layout_id' => $layout->id,
            'position' => $position
        ])->orderBy('ordering')->get();

        $plugins = [];
        foreach ($layout_plugins as $layout_plugin) {
            $plugins[] = Plugin::run($layout_plugin->plugin_id);
        }

        return $plugins;
    }
}
