<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests;

use TypeError;
use Vaened\Structer\Exceptions\MassAssignmentException;
use Vaened\Structer\Exceptions\UndefinedForeignKeyException;
use Vaened\Structer\Structurable;
use Vaened\Structer\Tests\Models\Country;
use Vaened\Structer\Tests\Models\Document;
use Vaened\Structer\Tests\Models\Gender;
use Vaened\Structer\Tests\Models\Structures\PersonStruct;

use function array_merge;
use function json_encode;
use function sprintf;

class StructurableTest extends TestCase
{
    public function test_create_from_properties_name(): void
    {
        $person = new PersonStruct($this->getAttributes());

        $this->assertInstanceOf(Structurable::class, $person);
    }

    public function test_create_from_column_names(): void
    {
        $attributes = [
            'id'         => 1,
            'first_name' => 'Enea',
            'last_name'  => 'Flores',
            'document'   => new Document('1', '12345678'),
            'sex'        => 'male'
        ];

        $person = new PersonStruct($attributes);
        $this->assertInstanceOf(Structurable::class, $person);
    }

    public function test_bind_a_model_to_a_property(): void
    {
        $person = new PersonStruct($this->getAttributes());

        $this->assertNull($person->getCountryID());

        $person->setRelatedModel(new Country(['id' => 1]));

        $this->assertEquals(1, $person->getCountryID());
    }

    public function test_convert_to_array_with_column_names(): void
    {
        $person = new PersonStruct($this->getAttributes());

        $this->assertEquals([
            'id'           => 1,
            'first_name'   => 'Enea',
            'last_name'    => 'Flores',
            'sex'          => null,
            'phone_number' => null,
            'country_id'   => null,
            'document'     => [
                'document_type_id' => 1,
                'document_number'  => '12345678'
            ]
        ], $person->toArray());
    }

    public function test_convert_to_json_with_column_names(): void
    {
        $person = new PersonStruct($this->getAttributes());

        $this->assertEquals(json_encode([
            'id'           => 1,
            'first_name'   => 'Enea',
            'last_name'    => 'Flores',
            'phone_number' => null,
            'country_id'   => null,
            'sex'          => null,
            'document'     => [
                'document_type_id' => 1,
                'document_number'  => '12345678'
            ]
        ], JSON_THROW_ON_ERROR), $person->toJson());
    }

    public function test_allow_and_ignore_unaccepted_values(): void
    {
        $this->turnOnMassAssignment();

        $person = new PersonStruct(array_merge($this->getAttributes(), ['non-existing' => 1]));

        $this->assertInstanceOf(PersonStruct::class, $person);
        $this->assertEquals([
            'id'           => 1,
            'first_name'   => 'Enea',
            'last_name'    => 'Flores',
            'phone_number' => null,
            'country_id'   => null,
            'sex'          => null,
            'document'     => [
                'document_type_id' => 1,
                'document_number'  => '12345678'
            ]
        ], $person->toArray());
    }

    public function test_throw_error_when_assigning_unaccepted_values(): void
    {
        $this->turnOffMassAssignment();

        $this->expectException(MassAssignmentException::class);
        $this->expectExceptionMessage(sprintf('The [non-existing] attribute is not assignable to %s', PersonStruct::class));

        new PersonStruct(['non-existing' => 1]);
    }

    public function test_throw_an_error_when_binding_an_unrelated_model_to_a_property(): void
    {
        $this->expectException(UndefinedForeignKeyException::class);
        $this->expectExceptionMessage(sprintf('The %s model does not have a foreign key defined', Gender::class));

        $person = new PersonStruct($this->getAttributes());
        $person->setRelatedModel(new Gender(['id' => 1]));
    }

    public function test_throw_error_create_structure_with_missing_attributes(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('Cannot assign null to property %s::$ID of type int', PersonStruct::class));

        new PersonStruct([]);
    }

    public function test_throw_error_when_assigning_a_different_data_type_to_the_property_signature(): void
    {
        $attributes = ['id' => 'P01'];

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(sprintf('Cannot assign string to property %s::$ID of type int', PersonStruct::class));

        new PersonStruct($attributes);
    }

    private function getAttributes(): array
    {
        return [
            'ID'        => 1,
            'firstName' => 'Enea',
            'lastName'  => 'Flores',
            'document'  => new Document('1', '12345678'),
        ];
    }
}
