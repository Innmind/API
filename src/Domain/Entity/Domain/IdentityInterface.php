<?php
declare(strict_types = 1);

namespace Domain\Entity\Domain;

interface IdentityInterface
{
    public function __toString(): string;
}
