<?php
declare(strict_types = 1);

namespace Domain\Handler\Image;

use Domain\{
    Command\Image\SpecifyDimension,
    Repository\ImageRepositoryInterface
};

final class SpecifyDimensionHandler
{
    private $repository;

    public function __construct(ImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SpecifyDimension $wished): void
    {
        $this
            ->repository
            ->get($wished->identity())
            ->specifyDimension($wished->dimension());
    }
}
