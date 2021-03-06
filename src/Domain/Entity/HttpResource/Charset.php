<?php
declare(strict_types = 1);

namespace Domain\Entity\HttpResource;

use Domain\Exception\DomainException;
use Innmind\Immutable\Str;

final class Charset
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Str::of($value)->matches('~^[a-zA-Z0-9\-_:\(\)]+$~')) {
            throw new DomainException;
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
