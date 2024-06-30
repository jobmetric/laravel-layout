<?php

namespace JobMetric\Layout\Exceptions;

use Exception;
use Throwable;

class ModelLayoutContractNotFoundException extends Exception
{
    public function __construct(string $model, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('layout::exceptions.model_layout_contract_not_found', [
            'model' => $model
        ]), $code, $previous);
    }
}
