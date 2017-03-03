<?php
declare(strict_types = 1);

namespace Tests\Domain\Command\Image;

use Domain\{
    Command\Image\AddDescription,
    Entity\Image\IdentityInterface,
    Entity\Image\Description
};
use PHPUnit\Framework\TestCase;

class AddDescriptionTest extends TestCase
{
    public function testInterface()
    {
        $command = new AddDescription(
            $identity = $this->createMock(IdentityInterface::class),
            $description = new Description('foobar')
        );

        $this->assertSame($identity, $command->identity());
        $this->assertSame($description, $command->description());
    }
}
