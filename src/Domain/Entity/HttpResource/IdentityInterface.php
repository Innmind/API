<?php
declare(strict_types = 1);

namespace Domain\Entity\HttpResource;

interface IdentityInterface
{
    public function __toString(): string;
}