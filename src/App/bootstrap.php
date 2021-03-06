<?php
declare(strict_types = 1);

namespace App;

use function Innmind\HttpTransport\bootstrap as http;
use function Innmind\Neo4j\DBAL\bootstrap as dbal;
use function Innmind\Neo4j\ONM\bootstrap as onm;
use function Innmind\CommandBus\bootstrap as commandBus;
use function Innmind\EventBus\bootstrap as eventBus;
use function Innmind\Logger\bootstrap as logger;
use function Innmind\Stack\stack;
use Innmind\Neo4j\ONM\{
    Metadata,
    Identity\Generator\UuidGenerator,
    Identity\Generator,
};
use Innmind\TimeContinuum\Earth\{
    Clock as Earth,
    Timezone\UTC,
};
use Innmind\Url\Url;
use Innmind\Filesystem\Adapter;
use Innmind\HttpTransport\Transport;
use Innmind\Immutable\{
    Map,
    Set,
};
use function Innmind\Immutable\unwrap;
use Pdp;

/**
 * @param callable(): Map<string, callable> $domain
 * @param Set<Url>|null $dsns
 */
function bootstrap(
    callable $domain,
    Transport $http,
    Url $neo4j,
    Adapter $domainEventStore,
    Set $dsns = null,
    string $activationLevel = ''
): array {
    /** @var Set<Url> */
    $dsns = $dsns ?? Set::of(Url::class);
    $domainParser = (new Pdp\Manager(
        new Pdp\Cache,
        new class implements Pdp\HttpClient {
            public function getContent(string $url): string
            {
                return \file_get_contents($url);
            }
        }
    ))->getRules();

    $clock = new Earth(new UTC);
    $log = http()['logger'](logger('http', ...unwrap($dsns))($activationLevel));
    $httpTransport = $log($http);

    $eventBuses = eventBus();
    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress InvalidScalarArgument
     */
    $eventBus = $eventBuses['bus'](
        Map::of('string', 'callable')
            ('Domain\Event\*', new Listener\StoreDomainEventListener($domainEventStore))
    );

    $dbal = dbal(
        $httpTransport,
        $clock,
        $neo4j
    );
    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     */
    $onm = onm(
        $dbal,
        Set::of(
            Metadata\Entity::class,
            ... (require __DIR__.'/config/neo4j.php')
        ),
        Map::of('string', Generator::class)
            ->put(Entity\Alternate\Identity::class, new UuidGenerator(Entity\Alternate\Identity::class))
            ->put(Entity\Author\Identity::class, new UuidGenerator(Entity\Author\Identity::class))
            ->put(Entity\Canonical\Identity::class, new UuidGenerator(Entity\Canonical\Identity::class))
            ->put(Entity\Citation\Identity::class, new UuidGenerator(Entity\Citation\Identity::class))
            ->put(Entity\CitationAppearance\Identity::class, new UuidGenerator(Entity\CitationAppearance\Identity::class))
            ->put(Entity\Domain\Identity::class, new UuidGenerator(Entity\Domain\Identity::class))
            ->put(Entity\DomainHost\Identity::class, new UuidGenerator(Entity\DomainHost\Identity::class))
            ->put(Entity\Host\Identity::class, new UuidGenerator(Entity\Host\Identity::class))
            ->put(Entity\HostResource\Identity::class, new UuidGenerator(Entity\HostResource\Identity::class))
            ->put(Entity\HtmlPage\Identity::class, new UuidGenerator(Entity\HtmlPage\Identity::class))
            ->put(Entity\HttpResource\Identity::class, new UuidGenerator(Entity\HttpResource\Identity::class))
            ->put(Entity\Image\Identity::class, new UuidGenerator(Entity\Image\Identity::class))
            ->put(Entity\Reference\Identity::class, new UuidGenerator(Entity\Reference\Identity::class))
            ->put(Entity\ResourceAuthor\Identity::class, new UuidGenerator(Entity\ResourceAuthor\Identity::class)),
        $eventBus
    );

    $authorRepository = new Repository\Neo4j\AuthorRepository(
        $onm['manager']->repository(\Domain\Entity\Author::class)
    );
    $resourceAuthorRepository = new Repository\Neo4j\ResourceAuthorRepository(
        $onm['manager']->repository(\Domain\Entity\ResourceAuthor::class)
    );
    $citationRepository = new Repository\Neo4j\CitationRepository(
        $onm['manager']->repository(\Domain\Entity\Citation::class)
    );
    $citationAppearanceRepository = new Repository\Neo4j\CitationAppearanceRepository(
        $onm['manager']->repository(\Domain\Entity\CitationAppearance::class)
    );
    $domainRepository = new Repository\Neo4j\DomainRepository(
        $onm['manager']->repository(\Domain\Entity\Domain::class)
    );
    $domainHostRepository = new Repository\Neo4j\DomainHostRepository(
        $onm['manager']->repository(\Domain\Entity\DomainHost::class)
    );
    $hostRepository = new Repository\Neo4j\HostRepository(
        $onm['manager']->repository(\Domain\Entity\Host::class)
    );
    $hostResourceRepository = new Repository\Neo4j\HostResourceRepository(
        $onm['manager']->repository(\Domain\Entity\HostResource::class)
    );
    $httpResourceRepository = new Repository\Neo4j\HttpResourceRepository(
        $onm['manager']->repository(\Domain\Entity\HttpResource::class)
    );
    $imageRepository = new Repository\Neo4j\ImageRepository(
        $onm['manager']->repository(\Domain\Entity\Image::class)
    );
    $htmlPageRepository = new Repository\Neo4j\HtmlPageRepository(
        $onm['manager']->repository(\Domain\Entity\HtmlPage::class)
    );
    $alternateRepository = new Repository\Neo4j\AlternateRepository(
        $onm['manager']->repository(\Domain\Entity\Alternate::class)
    );
    $canonicalRepository = new Repository\Neo4j\CanonicalRepository(
        $onm['manager']->repository(\Domain\Entity\Canonical::class)
    );
    $referenceRepository = new Repository\Neo4j\ReferenceRepository(
        $onm['manager']->repository(\Domain\Entity\Reference::class)
    );

    $handlers = $domain(
        $authorRepository,
        $citationRepository,
        $citationAppearanceRepository,
        $domainRepository,
        $hostRepository,
        $domainHostRepository,
        $hostResourceRepository,
        $httpResourceRepository,
        $resourceAuthorRepository,
        $imageRepository,
        $htmlPageRepository,
        $alternateRepository,
        $canonicalRepository,
        $referenceRepository,
        $domainParser,
        $clock
    );

    $commandBuses = commandBus();
    $log = $commandBuses['logger'](
        logger('app', ...unwrap($dsns))($activationLevel)
    );

    $commandBus = stack(
        $log,
        $onm['command_bus']['clear_domain_events'],
        $onm['command_bus']['dispatch_domain_events'],
        $onm['command_bus']['flush'],
    )($commandBuses['bus']($handlers));

    return [
        'command_bus' => $commandBus,
        // those repositories will be needed for the rest gateways, these should
        // not be exposed and replaced instead by a query bus
        'repository' => [
            'http_resource' => $httpResourceRepository,
            'image' => $imageRepository,
            'html_page' => $htmlPageRepository,
        ],
        'dbal' => $dbal,
    ];
}
