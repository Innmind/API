<?php
declare(strict_types = 1);

namespace Domain\Entity\Canonical;

interface IdentityInterface
{
    public function __toString(): string;
}