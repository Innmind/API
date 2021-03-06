<?php
declare(strict_types = 1);

namespace Domain\Event;

use Domain\Entity\Citation\{
    Identity,
    Text
};

final class CitationRegistered
{
    private Identity $identity;
    private Text $text;

    public function __construct(Identity $identity, Text $text)
    {
        $this->identity = $identity;
        $this->text = $text;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function text(): Text
    {
        return $this->text;
    }
}
