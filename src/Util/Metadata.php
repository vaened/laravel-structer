<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Util;

use Vaened\Structer\Annotations\Property;

final class Metadata
{
    private readonly string $property;

    private readonly ?string $column;

    private readonly ?string $references;

    public function __construct(string $propertyName, Property $annotation)
    {
        $this->property = $propertyName;
        $this->column = $annotation->getColumnName();
        $this->references = $annotation->getTableReference();
    }

    public function getPriorityName(): string
    {
        return $this->getColumnName() ?: $this->getPropertyName();
    }

    public function getPropertyName(): string
    {
        return $this->property;
    }

    public function getColumnName(): ?string
    {
        return $this->column;
    }

    public function getTableReference(): ?string
    {
        return $this->references;
    }
}
