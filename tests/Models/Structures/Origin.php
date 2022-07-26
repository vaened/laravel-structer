<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests\Models\Structures;

use Vaened\Structer\Fixed;

class Origin extends Fixed
{
    protected static function table(): string
    {
        return 'origins';
    }

    public function getDescription(): string
    {
        return $this->value('description');
    }

    public function getUniqueIdentification(): string
    {
        return $this->value('id');
    }
}
