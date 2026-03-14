<?php

namespace JobMetric\Layout\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when the model's layout collection field (from LayoutContract) does not exist on the model.
 */
class CollectionPropertyNotExistException extends Exception
{
    /**
     * @param class-string $model
     * @param string $field
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $model, string $field, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct(trans('layout::base.exceptions.collection_property_not_exist', [
            'field' => $field,
            'model' => $model,
        ]), $code, $previous);
    }
}
