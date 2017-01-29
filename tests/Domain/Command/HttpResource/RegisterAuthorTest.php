<?php
declare(strict_types = 1);

namespace Tests\Domain\Command\HttpResource;

use Domain\{
    Command\HttpResource\RegisterAuthor,
    Entity\ResourceAuthor\IdentityInterface,
    Entity\Author\IdentityInterface as AuthorIdentity,
    Entity\HttpResource\IdentityInterface as ResourceIdentity
};

class RegisterAuthorTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $command = new RegisterAuthor(
            $identity = $this->createMock(IdentityInterface::class),
            $author = $this->createMock(AuthorIdentity::class),
            $resource = $this->createMock(ResourceIdentity::class)
        );

        $this->assertSame($identity, $command->identity());
        $this->assertSame($author, $command->author());
        $this->assertSame($resource, $command->resource());
    }
}