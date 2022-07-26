<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use UnexpectedValueException;
use UnitEnum;
use function config;
use function json_encode;

/**
 * The Fixed class is used to represent a collection of static data.
 *
 * @package Vaened\Structer
 * @author enea dhack <enea.so@live.com>
 */
abstract class Fixed implements Arrayable, Jsonable, JsonSerializable
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    abstract protected static function table(): string;

    abstract public function getUniqueIdentification(): string|int|UnitEnum;

    public function value(string $column): mixed
    {
        return $this->attributes[$column] ?? null;
    }

    public function toArray(): array
    {
        return Arr::map($this->attributes, static fn(mixed $value) => $value instanceof Arrayable ? $value->toArray() : $value);
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function find(string|int|UnitEnum $unique): ?static
    {
        return static::collection()->first(fn(self $model): bool => $model->getUniqueIdentification() === $unique);
    }

    public static function collection(): Collection
    {
        return static::lists()->map(fn(array $model): static => new static($model));
    }

    public static function pluck(string $value, string $key = null): Collection
    {
        return static::lists()->pluck($value, $key);
    }

    protected static function lists(): Collection
    {
        $table = config(static::getTableName());

        if ($table === null) {
            throw new UnexpectedValueException(static::table() . " collection not found for " . static::class);
        }

        return collect($table);
    }

    protected static function getTableName(): string
    {
        return 'laravel-structer.collections.' . static::table();
    }
}
