<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Structer\Tests\Models\Structures;

enum Sex: string
{
    case Male = 'male';
    case Female = 'female';
}
