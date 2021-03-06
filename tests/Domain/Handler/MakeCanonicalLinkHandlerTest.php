<?php
declare(strict_types = 1);

namespace Tests\Domain\Handler;

use Domain\{
    Handler\MakeCanonicalLinkHandler,
    Command\MakeCanonicalLink,
    Repository\CanonicalRepository,
    Entity\Canonical,
    Entity\Canonical\Identity,
    Entity\HttpResource\Identity as ResourceIdentity,
    Specification\AndSpecification,
    Specification\Canonical\HttpResource,
    Specification\Canonical\Canonical as CanonicalSpec,
    Event\CanonicalCreated,
    Exception\CanonicalAlreadyExist,
};
use Innmind\TimeContinuum\{
    Clock,
    PointInTime,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class MakeCanonicalLinkHandlerTest extends TestCase
{
    public function testExecution()
    {
        $handler = new MakeCanonicalLinkHandler(
            $repository = $this->createMock(CanonicalRepository::class),
            $clock = $this->createMock(Clock::class)
        );
        $command = new MakeCanonicalLink(
            $this->createMock(Identity::class),
            $this->createMock(ResourceIdentity::class),
            $this->createMock(ResourceIdentity::class)
        );
        $command
            ->canonical()
            ->expects($this->once())
            ->method('toString')
            ->willReturn('canonical uuid');
        $command
            ->resource()
            ->expects($this->once())
            ->method('toString')
            ->willReturn('resource uuid');
        $clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(
                $now = $this->createMock(PointInTime::class)
            );
        $repository
            ->expects($this->once())
            ->method('matching')
            ->with($this->callback(function(AndSpecification $spec): bool {
                return $spec->left() instanceof HttpResource &&
                    $spec->right() instanceof CanonicalSpec &&
                    $spec->left()->value() === 'resource uuid' &&
                    $spec->right()->value() === 'canonical uuid';
            }))
            ->willReturn(Set::of(Canonical::class));
        $repository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function(Canonical $canonical) use ($command, $now): bool {
                return $canonical->identity() === $command->identity() &&
                    $canonical->canonical() === $command->canonical() &&
                    $canonical->resource() === $command->resource() &&
                    $canonical->foundAt() === $now &&
                    $canonical->recordedEvents()->size() === 1 &&
                    $canonical->recordedEvents()->first() instanceof CanonicalCreated;
            }));

        $this->assertNull($handler($command));
    }

    public function testThrowWhenCanonicalLinkAlreadyExist()
    {
        $handler = new MakeCanonicalLinkHandler(
            $repository = $this->createMock(CanonicalRepository::class),
            $clock = $this->createMock(Clock::class)
        );
        $command = new MakeCanonicalLink(
            $this->createMock(Identity::class),
            $this->createMock(ResourceIdentity::class),
            $this->createMock(ResourceIdentity::class)
        );
        $command
            ->canonical()
            ->expects($this->once())
            ->method('toString')
            ->willReturn('canonical uuid');
        $command
            ->resource()
            ->expects($this->once())
            ->method('toString')
            ->willReturn('resource uuid');
        $repository
            ->expects($this->once())
            ->method('matching')
            ->with($this->callback(function(AndSpecification $spec): bool {
                return $spec->left() instanceof HttpResource &&
                    $spec->right() instanceof CanonicalSpec &&
                    $spec->left()->value() === 'resource uuid' &&
                    $spec->right()->value() === 'canonical uuid';
            }))
            ->willReturn(
                Set::of(
                    Canonical::class,
                    new Canonical(
                        $this->createMock(Identity::class),
                        $this->createMock(ResourceIdentity::class),
                        $this->createMock(ResourceIdentity::class),
                        $this->createMock(PointInTime::class)
                    )
                )
            );
        $repository
            ->expects($this->never())
            ->method('add');
        $clock
            ->expects($this->never())
            ->method('now');

        $this->expectException(CanonicalAlreadyExist::class);

        $handler($command);
    }
}
