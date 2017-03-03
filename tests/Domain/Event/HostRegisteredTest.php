<?php
declare(strict_types = 1);

namespace Tests\Domain\Event;

use Domain\{
    Event\HostRegistered,
    Entity\Host\IdentityInterface,
    Entity\Host\Name
};
use PHPUnit\Framework\TestCase;

class HostRegisteredTest extends TestCase
{
    public function testInterface()
    {
        $event = new HostRegistered(
            $identity = $this->createMock(IdentityInterface::class),
            $name = new Name('www.example.com')
        );

        $this->assertSame($identity, $event->identity());
        $this->assertSame($name, $event->name());
    }
}
