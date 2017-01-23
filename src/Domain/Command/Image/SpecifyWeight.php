<?php
declare(strict_types = 1);

namespace Domain\Command\Image;

use Domain\Entity\Image\{
    IdentityInterface,
    Weight
};

final class SpecifyWeight
{
    private $identity;
    private $weight;

    public function __construct(IdentityInterface $identity, Weight $weight)
    {
        $this->identity = $identity;
        $this->weight = $weight;
    }

    public function identity(): IdentityInterface
    {
        return $this->identity;
    }

    public function weight(): Weight
    {
        return $this->weight;
    }
}
