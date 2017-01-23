<?php
declare(strict_types = 1);

namespace Domain\Entity\Domain;

use Domain\Exception\InvalidArgumentException;

final class TopLevelDomain
{
    private $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException;
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
