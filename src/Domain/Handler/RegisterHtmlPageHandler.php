<?php
declare(strict_types = 1);

namespace Domain\Handler;

use Domain\{
    Command\RegisterHtmlPage,
    Entity\HtmlPage,
    Entity\HostResource,
    Entity\HtmlPage\IdentityInterface,
    Entity\HttpResource\IdentityInterface as ResourceIdentity,
    Repository\HtmlPageRepositoryInterface,
    Repository\HostResourceRepositoryInterface,
    Specification\HttpResource\Path,
    Specification\HttpResource\Query,
    Specification\HostResource\InResources,
    Specification\HostResource\Host,
    Exception\HtmlPageAlreadyExistException
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Immutable\Set;

final class RegisterHtmlPageHandler
{
    private $htmlPageRepository;
    private $relationRepository;
    private $clock;

    public function __construct(
        HtmlPageRepositoryInterface $htmlPageRepository,
        HostResourceRepositoryInterface $relationRepository,
        TimeContinuumInterface $clock
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
     * @throws HtmlPageAlreadyExistException
     */
    private function verifyResourceDoesntExist(RegisterHtmlPage $wished): void
    {
        $htmlPages = $this->htmlPageRepository->matching(
            (new Path($wished->path()))
                ->and(new Query($wished->query()))
        );

        if ($htmlPages->size() === 0) {
            return;
        }

        $identities = $htmlPages->reduce(
            new Set(ResourceIdentity::class),
            function(Set $identities, HtmlPage $htmlPage): Set {
                return $identities->add($htmlPage->identity());
            }
        );
        $relations = $this->relationRepository->matching(
            (new InResources($identities))
                ->and(new Host($wished->host()))
        );

        if ($relations->size() > 0) {
            throw new HtmlPageAlreadyExistException;
        }
    }
}