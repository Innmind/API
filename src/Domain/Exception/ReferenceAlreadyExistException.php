<?php
declare(strict_types = 1);

namespace Domain\Exception;

use Domain\Entity\Reference;

final class ReferenceAlreadyExistException extends LogicException
{
    private $reference;

    public function __construct(Reference $reference)
    {
        $this->reference = $reference;
    }

    /**
     * The reference that already exist
     */
    public function reference(): Reference
    {
        return $this->reference;
    }
}
