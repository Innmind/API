<?php
declare(strict_types = 1);

namespace Domain\Specification\Reference;

use Domain\Entity\Reference as Entity;
use Innmind\Specification\Specification as ParentSpec;

interface Specification extends ParentSpec
{
    public function isSatisfiedBy(Entity $reference): bool;
}
