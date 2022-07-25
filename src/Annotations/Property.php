<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Property
{
    private ?string $column;

    private ?string $references;

    /**
     * @param string|null $column The name of the columns in the database, this will be used to replace the key when converting to an array.
     * @param string|null $references The referenced table, this is used to easily fill foreign keys.
     */
    public function __construct(?string $column = null, ?string $references = null)
    {
        $this->column = $column;
        $this->references = $references;
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
