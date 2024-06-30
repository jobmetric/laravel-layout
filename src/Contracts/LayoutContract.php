<?php

namespace JobMetric\Layout\Contracts;

interface LayoutContract
{
    /**
     * Layout page type.
     *
     * @return string
     */
    public function layoutPageType(): string;

    /**
     * Layout collection field.
     *
     * @return string|null
     */
    public function layoutCollectionField(): ?string;
}
