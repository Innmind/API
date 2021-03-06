<?php
declare(strict_types = 1);

namespace Domain\Handler\Citation;

use Domain\{
    Command\Citation\RegisterAppearance,
    Repository\CitationAppearanceRepository,
    Entity\CitationAppearance,
    Specification\CitationAppearance\Citation,
    Specification\CitationAppearance\HttpResource,
    Exception\CitationAppearanceAlreadyExist
};
use Innmind\TimeContinuum\Clock;
use function Innmind\Immutable\first;

final class RegisterAppearanceHandler
{
    private CitationAppearanceRepository $repository;
    private Clock $clock;

    public function __construct(
        CitationAppearanceRepository $repository,
        Clock $clock
    ) {
        $this->repository = $repository;
        $this->clock = $clock;
    }

    public function __invoke(RegisterAppearance $wished): void
    {
        /** @psalm-suppress InvalidArgument */
        $appearances = $this->repository->matching(
            (new Citation($wished->citation()))
                ->and(new HttpResource($wished->resource()))
        );

        if ($appearances->size() > 0) {
            throw new CitationAppearanceAlreadyExist(
                first($appearances)
            );
        }

        $this->repository->add(
            CitationAppearance::register(
                $wished->identity(),
                $wished->citation(),
                $wished->resource(),
                $this->clock->now()
            )
        );
    }
}
