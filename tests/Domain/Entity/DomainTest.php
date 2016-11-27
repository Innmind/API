<?php
declare(strict_types = 1);

namespace Tests\Domain\Entity;

use Domain\{
    Entity\Domain,
    Entity\Domain\IdentityInterface,
    Event\DomainRegistered
};
use Innmind\EventBus\ContainsRecordedEventsInterface;

class DomainTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $domain = new Domain(
            $identity = $this->createMock(IdentityInterface::class),
            'example',
            'com'
        );

        $this->assertInstanceOf(ContainsRecordedEventsInterface::class, $domain);
        $this->assertSame($identity, $domain->identity());
        $this->assertSame('example', $domain->name());
        $this->assertSame('com', $domain->tld());
        $this->assertSame('example.com', (string) $domain);
        $this->assertCount(0, $domain->recordedEvents());
    }

    /**
     * @expectedException Domain\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyName()
    {
        new Domain(
            $this->createMock(IdentityInterface::class),
            '',
            'com'
        );
    }

    /**
     * @expectedException Domain\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyTld()
    {
        new Domain(
            $this->createMock(IdentityInterface::class),
            'example',
            ''
        );
    }

    public function testRegister()
    {
        $domain = Domain::register(
            $identity = $this->createMock(IdentityInterface::class),
            'example',
            'com'
        );

        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertCount(1, $domain->recordedEvents());
        $this->assertInstanceOf(
            DomainRegistered::class,
            $domain->recordedEvents()->current()
        );
        $this->assertSame(
            $identity,
            $domain->recordedEvents()->current()->identity()
        );
        $this->assertSame(
            'example',
            $domain->recordedEvents()->current()->name()
        );
        $this->assertSame(
            'com',
            $domain->recordedEvents()->current()->tld()
        );
    }
}
