<?php
declare(strict_types = 1);

namespace Domain\Event\HttpResource;

use Domain\Entity\HttpResource\IdentityInterface;

final class CharsetSpecified
{
    private $identity;
    private $charset;

    public function __construct(IdentityInterface $identity, string $charset)
    {
        $this->identity = $identity;
        $this->charset = $charset;
    }

    public function identity(): IdentityInterface
    {
        return $this->identity;
    }

    public function charset(): string
    {
        return $this->charset;
    }
}
