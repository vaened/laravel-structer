<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer;

use Closure;
use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use Illuminate\Support\Arr;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;
use Vaened\Structer\Annotations\Property;
use Vaened\Structer\Exceptions\MassAssignmentException;
use Vaened\Structer\Util\Metadata;
use Vaened\Structer\Util\Relatable;
use Vaened\Structer\Util\Serializable;
use function array_filter;
use function config;
use function count;
use function in_array;

/**
 * The Structurable class is used to represent a model, makes it easy to decorate, validate, and is generated from a Laravel request
 *
 * @package Vaened\Structer
 * @author enea dhack <enea.so@live.com>
 */
abstract class Structurable implements Arrayable, Jsonable, JsonSerializable
{
    use Relatable, Serializable;

    private array $fields = [];

    public function __construct(array $attributes)
    {
        $this->setSecureAttributes($attributes);
    }

    private function setSecureAttributes(array $attributes): void
    {
        $properties = $this->getReflectionProperties();

        $this->catchMetadata($properties);
        $this->checkMassAssignment($attributes);
        $this->fillProperties($properties, $attributes);
    }

    private function catchMetadata(array $properties): void
    {
        foreach ($properties as $property) {
            $annotation = $property->getAttributes(Property::class)[0]->newInstance();
            $this->fields[] = new Metadata($property->getName(), $annotation);
        }
    }

    private function checkMassAssignment(array $attributes): void
    {
        if ($this->isAllowMassAssignment()) {
            return;
        }

        $names = $this->getAllAllowedPropertyNames();

        foreach ($attributes as $name => $attribute) {
            $this->validateAllowedAttribute($name, $names);
        }
    }

    private function getAllAllowedPropertyNames(): array
    {
        $names = [
            ...Arr::map($this->fields, static fn(Metadata $metadata) => $metadata->getColumnName()),
            ...Arr::map($this->fields, static fn(Metadata $metadata) => $metadata->getPropertyName()),
        ];

        return array_filter($names, static fn(?string $name) => $name !== null);
    }

    private function fillProperties(array $properties, array $attributes): void
    {
        foreach ($properties as $property) {
            $metadata = $this->getMetadataOf($property);
            $value = $this->extractValueOf($metadata, $attributes);
            $this->setValue($property, $value);
        }
    }

    private function getMetadataOf(ReflectionProperty $property): Metadata
    {
        return Arr::first($this->fields, static fn(Metadata $metadata) => $metadata->getPropertyName() === $property->getName());
    }

    private function extractValueOf(Metadata $metadata, array $attributes): mixed
    {
        if (isset($attributes[$metadata->getColumnName()])) {
            return $attributes[$metadata->getColumnName()];
        }

        if (isset($attributes[$metadata->getPropertyName()])) {
            return $attributes[$metadata->getPropertyName()];
        }

        return null;
    }

    private function setValue(ReflectionProperty $property, mixed $value): void
    {
        $property->setValue($this, $value);
    }

    private function validateAllowedAttribute(string $column, array $properties): void
    {
        if (! in_array($column, $properties, true)) {
            throw new MassAssignmentException("The [$column] attribute is not assignable to " . static::class);
        }
    }

    private function getReflectionProperties(): array
    {
        return array_filter($this->getReflectionClass()->getProperties(), $this->onlyMarkedProperties());
    }

    private function getReflectionClass(): ReflectionClass
    {
        return new ReflectionClass(static::class);
    }

    protected function isAllowMassAssignment(): bool
    {
        return config('laravel-structer.allow-mass-assignment', true);
    }

    protected function onlyMarkedProperties(): Closure
    {
        return static fn(ReflectionProperty $property) => count($property->getAttributes(Property::class)) > 0;
    }
}
