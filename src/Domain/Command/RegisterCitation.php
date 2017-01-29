<?php
declare(strict_types = 1);

namespace Domain\Command;

use Domain\Entity\Citation\{
    IdentityInterface,
    Text
};

final class RegisterCitation
{
    private $identity;
    private $text;

    public function __construct(IdentityInterface $identity, Text $text)
    {
        $this->identity = $identity;
        $this->text = $text;
    }

    public function identity(): IdentityInterface
    {
        return $this->identity;
    }

    public function text(): Text
    {
        return $this->text;
    }
}