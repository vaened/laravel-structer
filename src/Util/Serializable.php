<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Util;

use BackedEnum;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use ReflectionProperty;
use Vaened\Structer\Structurable;

use function array_reduce;
use function json_encode;

/**
 * @mixin Structurable
 */
trait Serializable
{
    abstract protected function outputDateFormat(): string;

    public function toArray(): array
    {
        $columns = array_reduce($this->getReflectionProperties(), $this->transformToColumnName(), []);

        return Arr::map($columns, fn($value) => match (true) {
            $value instanceof DateTimeInterface => $value->format($this->outputDateFormat()),
            $value instanceof BackedEnum => $value->value,
            $value instanceof Arrayable => $value->toArray(),
            default => $value
        });
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function transformToColumnName(): Closure
    {
        return function (array $acc, ReflectionProperty $property): array {
            $column       = $this->getMetadataOf($property)->getPriorityName();
            $acc[$column] = $property->getValue($this);
            return $acc;
        };
    }
}