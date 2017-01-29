<?php
declare(strict_types = 1);

namespace Domain\Entity\Image;

use Domain\Exception\InvalidArgumentException;

final class Description
{
    private $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException;
        }

        $this->value = $value;
    }

    public function equals(self $description): bool
    {
        return $this->value === $description->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}