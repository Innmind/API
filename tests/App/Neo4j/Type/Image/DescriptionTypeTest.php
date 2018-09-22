<?php
declare(strict_types = 1);

namespace Tests\App\Neo4j\Type\Image;

use App\Neo4j\Type\Image\DescriptionType;
use Domain\Entity\Image\Description;
use Innmind\Neo4j\ONM\{
    Type,
    Types
};
use Innmind\Immutable\{
    SetInterface,
    MapInterface
};
use PHPUnit\Framework\TestCase;

class DescriptionTypeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Type::class,
            new DescriptionType
        );
    }

    public function testIdentifiers()
    {
        $this->assertInstanceOf(
            SetInterface::class,
            DescriptionType::identifiers()
        );
        $this->assertSame('string', (string) DescriptionType::identifiers()->type());
        $this->assertSame(DescriptionType::identifiers(), DescriptionType::identifiers());
        $this->assertSame(
            ['image_description'],
            DescriptionType::identifiers()->toPrimitive()
        );
    }

    public function testFromConfig()
    {
        $this->assertInstanceOf(
            DescriptionType::class,
            DescriptionType::fromConfig(
                $this->createMock(MapInterface::class),
                new Types
            )
        );
    }

    public function testForDatabase()
    {
        $this->assertSame(
            'foo',
            (new DescriptionType)->forDatabase(
                new Description('foo')
            )
        );
    }

    public function testFromDatabase()
    {
        $description = (new DescriptionType)->fromDatabase('foo');
        $this->assertInstanceOf(Description::class, $description);
        $this->assertSame('foo', (string) $description);
    }

    public function testIsNullable()
    {
        $this->assertFalse((new DescriptionType)->isNullable());
    }
}