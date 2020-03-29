<?php
declare(strict_types = 1);

namespace Web\Gateway\HttpResourceGateway;

use App\Entity\{
    HttpResource\Identity,
    HostResource\Identity as HostResourceIdentity,
    Domain\Identity as DomainIdentity,
    Host\Identity as HostIdentity,
    DomainHost\Identity as DomainHostIdentity,
};
use Domain\{
    Command\RegisterDomain,
    Command\RegisterHost,
    Command\RegisterHttpResource,
    Command\HttpResource\SpecifyCharset,
    Command\HttpResource\SpecifyLanguages,
    Exception\DomainAlreadyExist,
    Exception\HostAlreadyExist,
    Entity\HttpResource\Charset,
    Model\Language,
};
use Innmind\Url\{
    Authority\Host,
    Path,
    Query,
};
use Innmind\Rest\Server\{
    ResourceCreator as ResourceCreatorInterface,
    Definition\HttpResource as ResourceDefinition,
    HttpResource,
    Identity as IdentityInterface,
};
use Innmind\CommandBus\CommandBus;
use Innmind\Immutable\Set;
use Ramsey\Uuid\Uuid;

final class ResourceCreator implements ResourceCreatorInterface
{
    private CommandBus $handle;

    public function __construct(CommandBus $handle)
    {
        $this->handle = $handle;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): IdentityInterface {
        $host = $this->registerHost($resource);
        $identity = $this->registerResource($resource, $host);
        $this->specifyCharset($resource, $identity);
        $this->specifyLanguages($resource, $identity);

        return $identity;
    }

    private function registerHost(HttpResource $resource): HostIdentity
    {
        try {
            ($this->handle)(
                new RegisterDomain(
                    $domain = new DomainIdentity((string) Uuid::uuid4()),
                    $host = Host::of($resource->property('host')->value())
                )
            );
        } catch (DomainAlreadyExist $e) {
            $domain = $e->domain()->identity();
        }

        try {
            ($this->handle)(
                new RegisterHost(
                    $identity = new HostIdentity((string) Uuid::uuid4()),
                    $domain,
                    new DomainHostIdentity((string) Uuid::uuid4()),
                    $host
                )
            );
        } catch (HostAlreadyExist $e) {
            $identity = $e->host()->identity();
        }

        return $identity;
    }

    private function registerResource(
        HttpResource $resource,
        HostIdentity $host
    ): Identity {
        $query = $resource->property('query')->value();

        ($this->handle)(
            new RegisterHttpResource(
                $identity = new Identity((string) Uuid::uuid4()),
                $host,
                new HostResourceIdentity((string) Uuid::uuid4()),
                Path::of($resource->property('path')->value()),
                empty($query) ? Query::none() : Query::of($query)
            )
        );

        return $identity;
    }

    private function specifyCharset(
        HttpResource $resource,
        Identity $identity
    ): void {
        if (!$resource->has('charset')) {
            return;
        }

        ($this->handle)(
            new SpecifyCharset(
                $identity,
                new Charset($resource->property('charset')->value())
            )
        );
    }

    private function specifyLanguages(
        HttpResource $resource,
        Identity $identity
    ): void {
        if (!$resource->has('languages')) {
            return;
        }

        $languages = $resource
            ->property('languages')
            ->value()
            ->mapTo(
                Language::class,
                static fn(string $language): Language => new Language($language),
            );

        ($this->handle)(
            new SpecifyLanguages(
                $identity,
                $languages
            )
        );
    }
}
