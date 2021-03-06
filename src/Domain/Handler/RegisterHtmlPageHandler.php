<?php
declare(strict_types = 1);

namespace Domain\Handler;

use Domain\{
    Command\RegisterHtmlPage,
    Entity\HtmlPage,
    Entity\HostResource,
    Entity\HttpResource\Identity as ResourceIdentity,
    Repository\HtmlPageRepository,
    Repository\HostResourceRepository,
    Specification\HttpResource\Path,
    Specification\HttpResource\Query,
    Specification\HostResource\InResources,
    Specification\HostResource\Host,
    Exception\HtmlPageAlreadyExist
};
use Innmind\TimeContinuum\Clock;
use Innmind\Immutable\Set;

final class RegisterHtmlPageHandler
{
    private HtmlPageRepository $htmlPageRepository;
    private HostResourceRepository $relationRepository;
    private Clock $clock;

    public function __construct(
        HtmlPageRepository $htmlPageRepository,
        HostResourceRepository $relationRepository,
        Clock $clock
    ) {
        $this->htmlPageRepository = $htmlPageRepository;
        $this->relationRepository = $relationRepository;
        $this->clock = $clock;
    }

    public function __invoke(RegisterHtmlPage $wished): void
    {
        $this->verifyResourceDoesntExist($wished);

        $htmlPage = HtmlPage::register(
            $wished->identity(),
            $wished->path(),
            $wished->query()
        );
        $relation = HostResource::create(
            $wished->relation(),
            $wished->host(),
            $wished->identity(),
            $this->clock->now()
        );

        $this->htmlPageRepository->add($htmlPage);
        $this->relationRepository->add($relation);
    }

    /**
     * @throws HtmlPageAlreadyExist
     */
    private function verifyResourceDoesntExist(RegisterHtmlPage $wished): void
    {
        /** @psalm-suppress InvalidArgument */
        $htmlPages = $this->htmlPageRepository->matching(
            (new Path($wished->path()))
                ->and(new Query($wished->query()))
        );

        if ($htmlPages->size() === 0) {
            return;
        }

        /** @var Set<ResourceIdentity> */
        $identities = $htmlPages->reduce(
            Set::of(ResourceIdentity::class),
            function(Set $identities, HtmlPage $htmlPage): Set {
                return $identities->add($htmlPage->identity());
            }
        );
        /** @psalm-suppress InvalidArgument */
        $relations = $this->relationRepository->matching(
            (new InResources($identities))
                ->and(new Host($wished->host()))
        );

        if ($relations->size() > 0) {
            throw new HtmlPageAlreadyExist;
        }
    }
}
