<?php
declare(strict_types = 1);

namespace Tests\Domain\Handler\HtmlPage;

use Domain\{
    Handler\HtmlPage\SpecifyThemeColourHandler,
    Command\HtmlPage\SpecifyThemeColour,
    Repository\HtmlPageRepositoryInterface,
    Entity\HtmlPage,
    Entity\HtmlPage\IdentityInterface,
    Event\HtmlPage\ThemeColourSpecified
};
use Innmind\Url\{
    PathInterface,
    QueryInterface
};
use Innmind\Colour\RGBA;
use PHPUnit\Framework\TestCase;

class SpecifyThemeColourHandlerTest extends TestCase
{
    public function testExecution()
    {
        $handler = new SpecifyThemeColourHandler(
            $repository = $this->createMock(HtmlPageRepositoryInterface::class)
        );
        $command = new SpecifyThemeColour(
            $this->createMock(IdentityInterface::class),
            RGBA::fromString('39f')
        );
        $repository
            ->expects($this->once())
            ->method('get')
            ->with($command->identity())
            ->willReturn(
                $page = new HtmlPage(
                    $command->identity(),
                    $this->createMock(PathInterface::class),
                    $this->createMock(QueryInterface::class)
                )
            );

        $this->assertNull($handler($command));
        $this->assertSame($command->colour(), $page->themeColour());
        $this->assertInstanceOf(
            ThemeColourSpecified::class,
            $page->recordedEvents()->current()
        );
    }
}
