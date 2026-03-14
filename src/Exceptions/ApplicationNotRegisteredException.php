<?php

namespace JobMetric\Layout\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an application key is not registered in ApplicationRegistry.
 *
 * @package JobMetric\Layout
 */
class ApplicationNotRegisteredException extends Exception
{
    /**
     * @param string $application Application key that is not registered.
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $application, int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct(trans('layout::base.exceptions.application_not_registered', [
            'application' => $application,
        ]), $code, $previous);
    }
}
