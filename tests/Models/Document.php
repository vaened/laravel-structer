<?php
/**
 * Created by enea dhack.
 */

namespace Vaened\Structer\Tests\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use function json_encode;

class Document implements Arrayable, Jsonable
{
    private int $documentTypeID;

    private string $documentNumber;

    public function __construct(int $documentTypeID, string $documentNumber)
    {
        $this->documentTypeID = $documentTypeID;
        $this->documentNumber = $documentNumber;
    }

    public function getDocumentTypeID(): int
    {
        return $this->documentTypeID;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function toArray(): array
    {
        return [
            'document_type_id' => $this->getDocumentTypeID(),
            'document_number' => $this->getDocumentNumber(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
