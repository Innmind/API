<?php
declare(strict_types = 1);

namespace Domain\Command;

use Domain\{
    Entity\Alternate\IdentityInterface,
    Entity\HttpResource\IdentityInterface as ResourceIdentity,
    Model\Language
};

final class RegisterAlternateResource
{
    private $identity;
    private $resource;
    private $alternate;
    private $language;

    public function __construct(
        IdentityInterface $identity,
        ResourceIdentity $resource,
        ResourceIdentity $alternate,
        Language $language
    ) {
        $this->identity = $identity;
        $this->resource = $resource;
        $this->alternate = $alternate;
        $this->language = $language;
    }

    public function identity(): IdentityInterface
    {
        return $this->identity;
    }

    public function resource(): ResourceIdentity
    {
        return $this->resource;
    }

    public function alternate(): ResourceIdentity
    {
        return $this->alternate;
    }

    public function language(): Language
    {
        return $this->language;
    }
}