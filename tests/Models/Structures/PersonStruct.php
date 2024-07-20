<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests\Models\Structures;

use Vaened\Structer\Annotations\Property;
use Vaened\Structer\Structurable;
use Vaened\Structer\Tests\Models\Document;

class PersonStruct extends Structurable
{
    #[Property(column: 'id')]
    protected int      $ID;

    #[Property(column: 'first_name')]
    protected string   $firstName;

    #[Property(column: 'last_name')]
    protected string   $lastName;

    #[Property(column: 'phone_number')]
    protected ?string  $phoneNumber;

    #[Property(column: 'country_id', references: 'countries')]
    protected ?string  $countryID;

    #[Property(column: 'sex')]
    protected ?Sex     $sex;

    #[Property]
    protected Document $document;

    public function getCountryID(): ?int
    {
        return $this->countryID;
    }
}
