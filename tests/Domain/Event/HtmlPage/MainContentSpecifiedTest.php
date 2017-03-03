<?php
declare(strict_types = 1);

namespace Tests\Domain\Event\HtmlPage;

use Domain\{
    Event\HtmlPage\MainContentSpecified,
    Entity\HtmlPage\IdentityInterface
};
use PHPUnit\Framework\TestCase;

class MainContentSpecifiedTest extends TestCase
{
    public function testInterface()
    {
        $event = new MainContentSpecified(
            $identity = $this->createMock(IdentityInterface::class),
            $mainContent = 'foo'
        );

        $this->assertSame($identity, $event->identity());
        $this->assertSame($mainContent, $event->mainContent());
    }
}
