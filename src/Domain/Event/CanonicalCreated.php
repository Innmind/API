<?php
declare(strict_types = 1);

namespace Domain\Event;

use Domain\{
    Entity\Canonical\Identity,
    Entity\HttpResource\Identity as ResourceIdentity
};
use Innmind\TimeContinuum\PointInTime;

final class CanonicalCreated
{
    private Identity $identity;
    private ResourceIdentity $canonical;
    private ResourceIdentity $resource;
    private PointInTime $foundAt;

    public function __construct(
        Identity $identity,
        ResourceIdentity $canonical,
        ResourceIdentity $resource,
        PointInTime $foundAt
    ) {
        $this->identity = $identity;
        $this->canonical = $canonical;
        $this->resource = $resource;
        $this->foundAt = $foundAt;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function canonical(): ResourceIdentity
    {
        return $this->canonical;
    }

    public function resource(): ResourceIdentity
    {
        return $this->resource;
    }

    public function foundAt(): PointInTime
    {
        return $this->foundAt;
    }
}
