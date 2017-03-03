<?php
declare(strict_types = 1);

namespace Tests\Domain\Command;

use Domain\{
    Command\RegisterCitation,
    Entity\Citation\IdentityInterface,
    Entity\Citation\Text
};
use PHPUnit\Framework\TestCase;

class RegisterCitationTest extends TestCase
{
    public function testInterface()
    {
        $command = new RegisterCitation(
            $identity = $this->createMock(IdentityInterface::class),
            $text = new Text('foo')
        );

        $this->assertSame($identity, $command->identity());
        $this->assertSame($text, $command->text());
    }
}
