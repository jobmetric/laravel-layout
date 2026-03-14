<?php

use JobMetric\Layout\HasLayout;

if (! function_exists('layout_relatable_cache_key')) {
    /**
     * Build cache key for a model's layout (getLayout result). Used by HasLayout and by cache invalidation.
     *
     * @param string $modelClass  Fully qualified model class name.
     * @param int|string|null $modelId Model primary key (null for new model).
     * @param string|null $application Application key.
     * @param string|null $collection  Collection key.
     *
     * @return string
     */
    function layout_relatable_cache_key(
        string $modelClass,
        int|string|null $modelId,
        ?string $application,
        ?string $collection
    ): string {
        $id = $modelId !== null ? (string) $modelId : '';

        return 'layout:relatable:' . $modelClass . ':' . $id . ':' . ($application ?? '') . ':' . ($collection ?? '');
    }
}

if (! function_exists('layout_model_uses_has_layout')) {
    /**
     * Check whether a model class or instance uses the HasLayout trait (recursively).
     *
     * @param object|string $model Model instance or class name.
     *
     * @return bool
     */
    function layout_model_uses_has_layout(object|string $model): bool
    {
        $class = is_object($model) ? get_class($model) : $model;

        if (! class_exists($class)) {
            return false;
        }

        if (function_exists('class_uses_recursive')) {
            return in_array(HasLayout::class, class_uses_recursive($class), true);
        }

        $results = [];
        $cursor = $class;

        do {
            $results = array_merge($results, class_uses($cursor) ?: []);
        } while ($cursor = get_parent_class($cursor));

        $stack = $results;

        while (! empty($stack)) {
            $trait = array_pop($stack);
            $nested = class_uses($trait) ?: [];

            foreach ($nested as $t) {
                if (! in_array($t, $results, true)) {
                    $results[] = $t;
                    $stack[] = $t;
                }
            }
        }

        return in_array(HasLayout::class, array_values(array_unique($results)), true);
    }
}
