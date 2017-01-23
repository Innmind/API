<?php
declare(strict_types = 1);

namespace Tests\Domain\Entity;

use Domain\{
    Entity\CitationAppearance,
    Entity\CitationAppearance\IdentityInterface,
    Entity\Citation\IdentityInterface as CitationIdentity,
    Entity\HttpResource\IdentityInterface as ResourceIdentity,
    Event\CitationAppearanceRegistered
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\EventBus\ContainsRecordedEventsInterface;

class CitationAppearanceTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $entity = new CitationAppearance(
            $identity = $this->createMock(IdentityInterface::class),
            $citation = $this->createMock(CitationIdentity::class),
            $resource = $this->createMock(ResourceIdentity::class),
            $foundAt = $this->createMock(PointInTimeInterface::class)
        );

        $this->assertInstanceOf(ContainsRecordedEventsInterface::class, $entity);
        $this->assertSame($identity, $entity->identity());
        $this->assertSame($citation, $entity->citation());
        $this->assertSame($resource, $entity->resource());
        $this->assertSame($foundAt, $entity->foundAt());
        $this->assertCount(0, $entity->recordedEvents());
    }

    public function testRegister()
    {
        $entity = CitationAppearance::register(
            $identity = $this->createMock(IdentityInterface::class),
            $citation = $this->createMock(CitationIdentity::class),
            $resource = $this->createMock(ResourceIdentity::class),
            $foundAt = $this->createMock(PointInTimeInterface::class)
        );

        $this->assertInstanceOf(CitationAppearance::class, $entity);
        $this->assertCount(1, $entity->recordedEvents());
        $this->assertInstanceOf(
            CitationAppearanceRegistered::class,
            $entity->recordedEvents()->current()
        );
        $this->assertSame(
            $identity,
            $entity->recordedEvents()->current()->identity()
        );
        $this->assertSame(
            $citation,
            $entity->recordedEvents()->current()->citation()
        );
        $this->assertSame(
            $resource,
            $entity->recordedEvents()->current()->resource()
        );
        $this->assertSame(
            $foundAt,
            $entity->recordedEvents()->current()->foundAt()
        );
    }
}
