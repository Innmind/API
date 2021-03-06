<?php
declare(strict_types = 1);

namespace Tests\Domain\Handler\Image;

use Domain\{
    Handler\Image\SpecifyWeightHandler,
    Command\Image\SpecifyWeight,
    Repository\ImageRepository,
    Entity\Image,
    Entity\Image\Identity,
    Entity\Image\Weight,
    Event\Image\WeightSpecified
};
use Innmind\Url\{
    Path,
    Query
};
use PHPUnit\Framework\TestCase;

class SpecifyWeightHandlerTest extends TestCase
{
    public function testExecution()
    {
        $handler = new SpecifyWeightHandler(
            $repository = $this->createMock(ImageRepository::class)
        );
        $command = new SpecifyWeight(
            $this->createMock(Identity::class),
            new Weight(12, 21)
        );
        $repository
            ->expects($this->once())
            ->method('get')
            ->with($command->identity())
            ->willReturn(
                $image = new Image(
                    $command->identity(),
                    Path::none(),
                    Query::none()
                )
            );

        $this->assertNull($handler($command));
        $this->assertCount(1, $image->recordedEvents());
        $this->assertInstanceOf(
            WeightSpecified::class,
            $image->recordedEvents()->first()
        );
        $this->assertSame($command->weight(), $image->weight());
    }
}
