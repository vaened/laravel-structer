<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $primaryKey = 'id';

    protected $guarded = [];
}
