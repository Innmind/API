<?php
declare(strict_types = 1);

namespace Tests\App\Repository\Neo4j;

use App\{
    Repository\Neo4j\ReferenceRepository,
    Entity\Reference\Identity,
};
use Domain\{
    Repository\ReferenceRepository as ReferenceRepositoryInterface,
    Entity\Reference,
    Entity\HttpResource\Identity as HttpResourceIdentity,
    Specification\Reference\Specification,
    Exception\ReferenceNotFound,
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound,
};
use Innmind\Immutable\Set;
use function Innmind\Immutable\unwrap;
use Ramsey\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ReferenceRepositoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ReferenceRepositoryInterface::class,
            new ReferenceRepository(
                $this->createMock(Repository::class)
            )
        );
    }

    public function testGet()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn(
                $expected = new Reference(
                    $identity,
                    $this->createMock(HttpResourceIdentity::class),
                    $this->createMock(HttpResourceIdentity::class)
                )
            );

        $this->assertSame($expected, $repository->get($identity));
    }

    public function testThrowWhenGettingUnknownEntity()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->will(
                $this->throwException(new EntityNotFound)
            );

        $this->expectException(ReferenceNotFound::class);
        $this->expectExceptionMessage('');
        $this->expectExceptionCode(0);

        $repository->get($identity);
    }

    public function testAdd()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $entity = new Reference(
            new Identity((string) Uuid::uuid4()),
            $this->createMock(HttpResourceIdentity::class),
            $this->createMock(HttpResourceIdentity::class)
        );
        $infra
            ->expects($this->once())
            ->method('add')
            ->with($entity);

        $this->assertSame($repository, $repository->add($entity));
    }

    public function testRemove()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->once())
            ->method('get')
            ->with($identity)
            ->willReturn(
                $entity = new Reference(
                    $identity,
                    $this->createMock(HttpResourceIdentity::class),
                    $this->createMock(HttpResourceIdentity::class)
                )
            );
        $infra
            ->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->assertSame($repository, $repository->remove($identity));
    }

    public function testHas()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $identity = new Identity((string) Uuid::uuid4());
        $infra
            ->expects($this->at(0))
            ->method('contains')
            ->with($identity)
            ->willReturn(true);
        $infra
            ->expects($this->at(1))
            ->method('contains')
            ->with($identity)
            ->willReturn(false);

        $this->assertTrue($repository->has($identity));
        $this->assertFalse($repository->has($identity));
    }

    public function testCount()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $infra
            ->expects($this->once())
            ->method('all')
            ->willReturn(
                Set::of(
                    Reference::class,
                    new Reference(
                        new Identity((string) Uuid::uuid4()),
                        $this->createMock(HttpResourceIdentity::class),
                        $this->createMock(HttpResourceIdentity::class)
                    )
                )
            );

        $this->assertSame(1, $repository->count());
    }

    public function testAll()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $infra
            ->expects($this->once())
            ->method('all')
            ->willReturn(
                Set::objects(
                    $entity = new Reference(
                        new Identity((string) Uuid::uuid4()),
                        $this->createMock(HttpResourceIdentity::class),
                        $this->createMock(HttpResourceIdentity::class)
                    )
                )
            );

        $all = $repository->all();

        $this->assertInstanceOf(Set::class, $all);
        $this->assertSame(Reference::class, (string) $all->type());
        $this->assertSame([$entity], unwrap($all));
    }

    public function testMatching()
    {
        $repository = new ReferenceRepository(
            $infra = $this->createMock(Repository::class)
        );
        $specification = $this->createMock(Specification::class);
        $infra
            ->expects($this->once())
            ->method('matching')
            ->with($specification)
            ->willReturn(
                Set::objects(
                    $entity = new Reference(
                        new Identity((string) Uuid::uuid4()),
                        $this->createMock(HttpResourceIdentity::class),
                        $this->createMock(HttpResourceIdentity::class)
                    )
                )
            );

        $all = $repository->matching($specification);

        $this->assertInstanceOf(Set::class, $all);
        $this->assertSame(Reference::class, (string) $all->type());
        $this->assertSame([$entity], unwrap($all));
    }
}
