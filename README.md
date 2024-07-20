# laravel-structer
Helpers for typical structures in a development


[![Build Status](https://github.com/vaened/laravel-structer/actions/workflows/tests.yml/badge.svg)](https://github.com/vaened/laravel-structer/actions?query=workflow%3ATests)  [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)



```php
class PersonStruct extends Structurable
{
    #[Property(column: 'id')]
    protected int $ID;

    #[Property(column: 'first_name')]
    protected string $firstName;

    #[Property(column: 'last_name')]
    protected string $lastName;

    #[Property(column: 'phone_number')]
    protected ?string $phoneNumber;

    #[Property(column: 'country_id', references: 'countries')]
    protected ?string $countryID;

    #[Property(column: 'sex')]
    protected ?Sex     $sex;

    #[Property(column: 'birth_date')]
    protected ?Carbon  $birthDate;

    #[Property]
    protected Document $document;
}
```