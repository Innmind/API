<?php
declare(strict_types = 1);

namespace Tests\AppBundle\Repository\Neo4j;

use AppBundle\{
    Repository\Neo4j\CitationRepository,
    Entity\Citation\Identity
};
use Domain\{
    Repository\CitationRepositoryInterface,
    Entity\Citation,
    Entity\Citation\Text,
    Specification\Citation\SpecificationInterface
};
use Innmind\Neo4j\ONM\RepositoryInterface;
use Innmind\Immutable\{
    SetInterface,
    Set
};
use Ramsey\Uuid\Uuid;

class CitationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CitationRepositoryInterface::class,
            new CitationRepository(
                $this->createMock(RepositoryInterface::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn(
                $expected = new Citation(
                    $identity,
                    new Text('foo')
                )
            );

        $this->assertSame($expected, $repository->get($identity));
    }

    public function testAdd()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $citation = new Citation(
            new Identity((string) Uuid::uuid4()),
            new Text('foo')
        );
        $infra
            ->expects($this->once())
            ->method('add')
            ->with($citation);

        $this->assertSame($repository, $repository->add($citation));
    }

    public function testRemove()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn(
                $citation = new Citation(
                    $identity,
                    new Text('foo')
                )
            );
        $infra
            ->expects($this->once())
            ->method('remove')
            ->with($citation);

        $this->assertSame($repository, $repository->remove($identity));
    }

    public function testHas()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->at(0))
            ->method('has')
            ->with($identity)
            ->willReturn(true);
        $infra
            ->expects($this->at(1))
            ->method('has')
            ->with($identity)
            ->willReturn(false);

        $this->assertTrue($repository->has($identity));
        $this->assertFalse($repository->has($identity));
    }

    public function testCount()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $infra
            ->expects($this->once())
            ->method('all')
            ->willReturn(
                $all = $this->createMock(SetInterface::class)
            );
        $all
            ->expects($this->once())
            ->method('size')
            ->willReturn(42);

        $this->assertSame(42, $repository->count());
    }

    public function testAll()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $infra
            ->expects($this->once())
            ->method('all')
            ->willReturn(
                (new Set('object'))->add(
                    $citation = new Citation(
                        new Identity((string) Uuid::uuid4()),
                        new Text('foo')
                    )
                )
            );

        $all = $repository->all();

        $this->assertInstanceOf(SetInterface::class, $all);
        $this->assertSame(Citation::class, (string) $all->type());
        $this->assertSame([$citation], $all->toPrimitive());
    }

    public function testMatching()
    {
        $repository = new CitationRepository(
            $infra = $this->createMock(RepositoryInterface::class)
        );
        $specification = $this->createMock(SpecificationInterface::class);
        $infra
            ->expects($this->once())
            ->method('matching')
            ->with($specification)
            ->willReturn(
                (new Set('object'))->add(
                    $citation = new Citation(
                        new Identity((string) Uuid::uuid4()),
                        new Text('foo')
                    )
                )
            );

        $all = $repository->matching($specification);

        $this->assertInstanceOf(SetInterface::class, $all);
        $this->assertSame(Citation::class, (string) $all->type());
        $this->assertSame([$citation], $all->toPrimitive());
    }
}
