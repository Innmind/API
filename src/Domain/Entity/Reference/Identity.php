<?php
declare(strict_types = 1);

namespace Domain\Entity\Reference;

interface Identity
{
    public function toString(): string;
}
