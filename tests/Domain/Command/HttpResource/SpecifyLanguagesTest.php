<?php
declare(strict_types = 1);

namespace Tests\Domain\Command\HttpResource;

use Domain\{
    Command\HttpResource\SpecifyLanguages,
    Entity\HttpResource\IdentityInterface,
    Model\Language
};
use Innmind\Immutable\Set;

class SpecifyLanguagesTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $command = new SpecifyLanguages(
            $identity = $this->createMock(IdentityInterface::class),
            $languages = (new Set(Language::class))
                ->add(new Language('fr'))
        );

        $this->assertSame($identity, $command->identity());
        $this->assertSame($languages, $command->languages());
    }

    /**
     * @expectedException Domain\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidSetOfLanguages()
    {
        new SpecifyLanguages(
            $this->createMock(IdentityInterface::class),
            new Set('string')
        );
    }
}