<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Util;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Vaened\Structer\Exceptions\UndefinedForeignKeyException;
use Vaened\Structer\Structurable;
use function array_filter;
use function count;
use function get_class;

/**
 * Provides the necessary functionality to link a model to an existing property previously configured.
 *
 * @mixin Structurable
 */
trait Relatable
{
    public function setRelatedModel(Model $model, string $foreign = null): void
    {
        $fields = array_filter($this->fields, $this->onlyReferencedFieldOf($model));
        $field = $this->resolveFieldProperty($fields, $foreign);

        $this->checkPropertyExists($model, $field);

        $property = $this->getReflectionClass()->getProperty($field?->getPropertyName());
        $this->setValue($property, $model->getKey());
    }

    private function onlyReferencedFieldOf(Model $model): Closure
    {
        return static fn(Metadata $metadata) => $metadata->getTableReference() === $model->getTable();
    }

    private function resolveFieldProperty(array $fields, ?string $foreign): ?Metadata
    {
        if (count($fields) === 1) {
            return Arr::first($fields);
        }

        return Arr::first($fields, $this->findForeignKeyProperty($foreign));
    }

    private function findForeignKeyProperty(?string $foreign): Closure
    {
        return static fn(Metadata $metadata) => $metadata->getColumnName() === $foreign || $metadata->getPropertyName() === $foreign;
    }

    private function checkPropertyExists(Model $model, ?Metadata $field): void
    {
        if ($field === null) {
            $clazz = get_class($model);
            throw new UndefinedForeignKeyException("The $clazz model does not have a foreign key defined");
        }
    }
}