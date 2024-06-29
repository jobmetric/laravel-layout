<?php

namespace JobMetric\Layout;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use JobMetric\Extension\Http\Resources\ExtensionResource;
use JobMetric\Layout\Http\Resources\LayoutResource;
use JobMetric\Layout\Models\Layout as LayoutModel;
use Spatie\QueryBuilder\QueryBuilder;

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
     * @param string $mode
     *
     * @return QueryBuilder
     */
    private function query(array $filter = [], array $with = [], string $mode = 'withTrashed'): QueryBuilder
    {
        $fields = ['id', 'name', 'status', 'created_at', 'updated_at'];

        $query = QueryBuilder::for(LayoutModel::class);

        if ($mode === 'withTrashed') {
            $query->withTrashed();
        }

        if($mode === 'onlyTrashed') {
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
     * @param string $mode
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = 'withTrashed'): AnonymousResourceCollection
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
     * @param string $mode
     *
     * @return AnonymousResourceCollection
     */
    public function all(array $filter = [], array $with = [], string $mode = 'withTrashed'): AnonymousResourceCollection
    {
        return LayoutResource::collection(
            $this->query($filter, $with, $mode)->get()
        );
    }
}
