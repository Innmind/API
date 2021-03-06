<?php
declare(strict_types = 1);

namespace Domain\Entity;

use Domain\{
    Entity\Citation\Identity,
    Entity\Citation\Text,
    Event\CitationRegistered,
};
use Innmind\EventBus\{
    ContainsRecordedEvents,
    EventRecorder,
};

final class Citation implements ContainsRecordedEvents
{
    use EventRecorder;

    private Identity $identity;
    private Text $text;

    public function __construct(Identity $identity, Text $text)
    {
        $this->identity = $identity;
        $this->text = $text;
    }

    public static function register(Identity $identity, Text $text): self
    {
        $self = new self($identity, $text);
        $self->record(new CitationRegistered($identity, $text));

        return $self;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function text(): Text
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }
}
