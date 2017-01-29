<?php
declare(strict_types = 1);

namespace Domain\Entity\Author;

interface IdentityInterface
{
    public function __toString(): string;
}