<?php
declare(strict_types = 1);

namespace App\Repository\Neo4j;

use Domain\{
    Repository\ResourceAuthorRepository as ResourceAuthorRepositoryInterface,
    Entity\ResourceAuthor,
    Entity\ResourceAuthor\Identity,
    Exception\ResourceAuthorNotFound,
    Specification\ResourceAuthor\Specification
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound
};
use Innmind\Immutable\Set;

final class ResourceAuthorRepository implements ResourceAuthorRepositoryInterface
{
    private Repository $infrastructure;

    public function __construct(Repository $infrastructure)
    {
        $this->infrastructure = $infrastructure;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Identity $identity): ResourceAuthor
    {
        try {
            return $this->infrastructure->get($identity);
        } catch (EntityNotFound $e) {
            throw new ResourceAuthorNotFound('', 0, $e);
        }
    }

    public function add(ResourceAuthor $entity): ResourceAuthorRepositoryInterface
    {
        $this->infrastructure->add($entity);

        return $this;
    }

    public function remove(Identity $identity): ResourceAuthorRepositoryInterface
    {
        $this->infrastructure->remove(
            $this->get($identity)
        );

        return $this;
    }

    public function has(Identity $identity): bool
    {
        return $this->infrastructure->contains($identity);
    }

    public function count(): int
    {
        return $this->infrastructure->all()->size();
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Set
    {
        return $this
            ->infrastructure
            ->all()
            ->reduce(
                Set::of(ResourceAuthor::class),
                function(Set $all, ResourceAuthor $entity): Set {
                    return $all->add($entity);
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Specification $specification): Set
    {
        return $this
            ->infrastructure
            ->matching($specification)
            ->reduce(
                Set::of(ResourceAuthor::class),
                function(Set $all, ResourceAuthor $entity): Set {
                    return $all->add($entity);
                }
            );
    }
}
