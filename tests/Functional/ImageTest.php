<?php
declare(strict_types = 1);

namespace Tests\Functional;

use function Web\bootstrap as web;
use Domain\{
    Repository\HttpResourceRepository,
    Repository\ImageRepository,
    Repository\HtmlPageRepository,
    Exception\ImageNotFound,
};
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Neo4j\DBAL\Connection;
use Innmind\Http\{
    Message\ServerRequest\ServerRequest,
    Message\Response,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion,
    Headers\Headers,
    Header\Accept,
    Header\AcceptValue,
};
use Innmind\Url\Url;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testNotFound()
    {
        $handle = web(
            $this->createMock(CommandBusInterface::class),
            $this->createMock(Connection::class),
            $this->createMock(HttpResourceRepository::class),
            $repository = $this->createMock(ImageRepository::class),
            $this->createMock(HtmlPageRepository::class)
        );
        $repository
            ->expects($this->once())
            ->method('get')
            ->will($this->throwException(new ImageNotFound));

        $response = $handle(new ServerRequest(
            Url::fromString('http://localhost/api/web/image/fc1cc2b4-2c09-43b1-8c19-3499068950ed'),
            Method::get(),
            new ProtocolVersion(1, 1),
            Headers::of(
                new Accept(
                    new AcceptValue('application', 'json')
                )
            )
        ));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->statusCode()->value());
        $this->assertSame('', (string) $response->body());
    }

    public function testOptions()
    {
        $handle = web(
            $this->createMock(CommandBusInterface::class),
            $this->createMock(Connection::class),
            $this->createMock(HttpResourceRepository::class),
            $this->createMock(ImageRepository::class),
            $this->createMock(HtmlPageRepository::class)
        );

        $response = $handle(new ServerRequest(
            Url::fromString('http://localhost/api/web/image/'),
            Method::options(),
            new ProtocolVersion(1, 1),
            Headers::of(
                new Accept(
                    new AcceptValue('application', 'json')
                )
            )
        ));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->statusCode()->value());
        $this->assertSame(
            'Content-Type: application/json',
            (string) $response->headers()->get('content-type')
        );
        $this->assertSame(
            '{"identity":"identity","properties":{"identity":{"type":"string","access":["READ"],"variants":[],"optional":false},"host":{"type":"string","access":["READ","CREATE"],"variants":[],"optional":false},"path":{"type":"string","access":["READ","CREATE"],"variants":[],"optional":false},"query":{"type":"string","access":["READ","CREATE"],"variants":[],"optional":false},"descriptions":{"type":"set<string>","access":["READ","CREATE"],"variants":[],"optional":true},"dimension":{"type":"map<string, int>","access":["READ","CREATE"],"variants":[],"optional":true},"weight":{"type":"int","access":["READ","CREATE"],"variants":[],"optional":true}},"metas":{"allowed_media_types":["image\/*"]},"rangeable":true,"linkable_to":[]}',
            (string) $response->body()
        );
    }
}
