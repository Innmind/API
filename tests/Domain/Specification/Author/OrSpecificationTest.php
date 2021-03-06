<?php
declare(strict_types = 1);

namespace Tests\Domain\Specification\Author;

use Domain\{
    Specification\Author\OrSpecification,
    Specification\Author\Specification,
    Specification\OrSpecification as ParentSpec,
    Entity\Author,
    Entity\Author\Identity,
    Entity\Author\Name,
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
        $author = new Author(
            $this->createMock(Identity::class),
            new Name('foo')
        );
        $spec
            ->left()
            ->expects($this->at(0))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(false);
        $spec
            ->left()
            ->expects($this->at(1))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(true);
        $spec
            ->left()
            ->expects($this->at(2))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(true);
        $spec
            ->left()
            ->expects($this->at(3))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(false);
        $spec
            ->right()
            ->expects($this->at(0))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(false);
        $spec
            ->right()
            ->expects($this->at(1))
            ->method('isSatisfiedBy')
            ->with($author)
            ->willReturn(true);

        $this->assertFalse($spec->isSatisfiedBy($author));
        $this->assertTrue($spec->isSatisfiedBy($author));
        $this->assertTrue($spec->isSatisfiedBy($author));
        $this->assertTrue($spec->isSatisfiedBy($author));
    }
}
