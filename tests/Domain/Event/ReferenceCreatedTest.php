<?php
declare(strict_types = 1);

namespace Tests\Domain\Event;

use Domain\{
    Event\ReferenceCreated,
    Entity\Reference\IdentityInterface,
    Entity\HttpResource\IdentityInterface as ResourceIdentity
};

class ReferenceCreatedTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $event = new ReferenceCreated(
            $identity = $this->createMock(IdentityInterface::class),
            $source = $this->createMock(ResourceIdentity::class),
            $target = $this->createMock(ResourceIdentity::class)
        );

        $this->assertSame($identity, $event->identity());
        $this->assertSame($source, $event->source());
        $this->assertSame($target, $event->target());
    }
}
