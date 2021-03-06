<?php
declare(strict_types = 1);

namespace Tests\Domain\Specification\HttpResource;

use Domain\{
    Specification\HttpResource\OrSpecification,
    Specification\HttpResource\Specification,
    Specification\OrSpecification as ParentSpec,
    Entity\HttpResource,
    Entity\HttpResource\Identity,
};
use Innmind\Url\{
    Path,
    Query,
};
use PHPUnit\Framework\TestCase;

class OrSpecificationTest extends TestCase
{
    public function testInterface()
    {
        $spec = new OrSpecification(
            $this->createMock(Specification::class),
            $this->createMock(Specification::class)
        );

        $this->assertInstanceOf(ParentSpec::class, $spec);
        $this->assertInstanceOf(Specification::class, $spec);
    }

    public function testIsSatisfiedBy()
    {
        $spec = new OrSpecification(
            $this->createMock(Specification::class),
            $this->createMock(Specification::class)
        );
        $resource = new HttpResource(
            $this->createMock(Identity::class),
            Path::none(),
            Query::none()
        );
        $spec
            ->left()
            ->expects($this->at(0))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(false);
        $spec
            ->left()
            ->expects($this->at(1))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(true);
        $spec
            ->left()
            ->expects($this->at(2))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(true);
        $spec
            ->left()
            ->expects($this->at(3))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(false);
        $spec
            ->right()
            ->expects($this->at(0))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(false);
        $spec
            ->right()
            ->expects($this->at(1))
            ->method('isSatisfiedBy')
            ->with($resource)
            ->willReturn(true);

        $this->assertFalse($spec->isSatisfiedBy($resource));
        $this->assertTrue($spec->isSatisfiedBy($resource));
        $this->assertTrue($spec->isSatisfiedBy($resource));
        $this->assertTrue($spec->isSatisfiedBy($resource));
    }
}
