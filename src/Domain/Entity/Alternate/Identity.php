<?php
declare(strict_types = 1);

namespace Domain\Entity\Alternate;

interface Identity
{
    public function toString(): string;
}
