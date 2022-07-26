<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests;

use Illuminate\Support\Collection;
use Vaened\Structer\Tests\Models\OriginCode;
use Vaened\Structer\Tests\Models\Structures\Origin;
use function collect;
use function json_encode;

class FixedTest extends TestCase
{
    public function test_get_collection(): void
    {
        $origins = Origin::collection();
        $this->assertEquals($this->collection(), $origins);
    }

    public function test_find_by_unique_identification(): void
    {
        $origin = Origin::find(OriginCode::EMERGENCY);

        $this->assertEquals(new Origin([
            'id' => OriginCode::EMERGENCY,
            'description' => 'Emergency',
        ]), $origin);
    }

    public function test_get_value_of_fixed(): void
    {
        $origin = Origin::find(OriginCode::HOSPITALIZATION);

        $this->assertEquals('Hospitalization', $origin->value('description'));
    }

    public function test_pluck_associative_values(): void
    {
        $origins = Origin::pluck('description', 'id');

        $this->assertEquals(collect([
            OriginCode::HOSPITALIZATION => 'Hospitalization',
            OriginCode::EXTERNAL => 'External',
            OriginCode::EMERGENCY => 'Emergency',
        ]), $origins);
    }

    public function test_pluck_plain_values(): void
    {
        $origins = Origin::pluck('id');

        $this->assertEquals(collect([
            OriginCode::HOSPITALIZATION,
            OriginCode::EXTERNAL,
            OriginCode::EMERGENCY,
        ]), $origins);
    }

    public function test_convert_to_array(): void
    {
        $origins = Origin::collection()->toArray();
        $origin = Origin::find(OriginCode::HOSPITALIZATION)->toArray();

        $this->assertEquals($this->collection()->toArray(), $origins);
        $this->assertEquals([
            'id' => OriginCode::HOSPITALIZATION,
            'description' => 'Hospitalization',
        ], $origin);
    }

    public function test_convert_to_json(): void
    {
        $origins = Origin::collection()->toJson();
        $origin = Origin::find(OriginCode::HOSPITALIZATION)->toJson();

        $this->assertEquals($this->collection()->toJson(), $origins);
        $this->assertEquals(json_encode([
            'id' => OriginCode::HOSPITALIZATION,
            'description' => 'Hospitalization',
        ], JSON_THROW_ON_ERROR), $origin);
    }

    private function collection(): Collection
    {
        return collect([
            new Origin([
                'id' => OriginCode::HOSPITALIZATION,
                'description' => 'Hospitalization',
            ]),
            new Origin([
                'id' => OriginCode::EXTERNAL,
                'description' => 'External',
            ]),
            new Origin([
                'id' => OriginCode::EMERGENCY,
                'description' => 'Emergency',
            ]),
        ]);
    }
}
