<?php
declare(strict_types = 1);

namespace Tests\AppBundle\Neo4j\Type\HtmlPage;

use AppBundle\Neo4j\Type\HtmlPage\AnchorType;
use Domain\Entity\HtmlPage\Anchor;
use Innmind\Neo4j\ONM\TypeInterface;
use Innmind\Immutable\{
    SetInterface,
    CollectionInterface
};

class AnchorTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            TypeInterface::class,
            new AnchorType
        );
    }

    public function testIdentifiers()
    {
        $this->assertInstanceOf(
            SetInterface::class,
            AnchorType::identifiers()
        );
        $this->assertSame('string', (string) AnchorType::identifiers()->type());
        $this->assertSame(AnchorType::identifiers(), AnchorType::identifiers());
        $this->assertSame(
            ['html_page_anchor'],
            AnchorType::identifiers()->toPrimitive()
        );
    }

    public function testFromConfig()
    {
        $this->assertInstanceOf(
            AnchorType::class,
            AnchorType::fromConfig(
                $this->createMock(CollectionInterface::class)
            )
        );
    }

    public function testForDatabase()
    {
        $this->assertSame(
            'foo',
            (new AnchorType)->forDatabase(
                new Anchor('foo')
            )
        );
    }

    public function testFromDatabase()
    {
        $anchor = (new AnchorType)->fromDatabase('foo');
        $this->assertInstanceOf(Anchor::class, $anchor);
        $this->assertSame('foo', $anchor->value());
    }

    public function testIsNullable()
    {
        $this->assertFalse((new AnchorType)->isNullable());
    }
}