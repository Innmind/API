<?php
declare(strict_types = 1);

namespace Domain\Event\Image;

use Domain\Entity\Image\IdentityInterface;

final class DescriptionAdded
{
    private $identity;
    private $description;

    public function __construct(IdentityInterface $identity, string $description)
    {
        $this->identity = $identity;
        $this->description = $description;
    }

    public function identity(): IdentityInterface
    {
        return $this->identity;
    }

    public function description(): string
    {
        return $this->description;
    }
}
