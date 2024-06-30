<?php

namespace JobMetric\Layout\Exceptions;

use Exception;
use Throwable;

class CollectionPropertyNotExistException extends Exception
{
    public function __construct(string $model, string $field, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('layout::base.exceptions.collection_property_not_exist', [
            'model' => $model,
            'field' => $field,
        ]), $code, $previous);
    }
}
