<?php
declare(strict_types = 1);

namespace AppBundle\Repository\Neo4j;

use Domain\{
    Repository\DomainHostRepositoryInterface,
    Entity\DomainHost,
    Entity\DomainHost\IdentityInterface,
    Specification\DomainHost\SpecificationInterface
};
use Innmind\Neo4j\ONM\RepositoryInterface;
use Innmind\Immutable\{
    SetInterface,
    Set
};

final class DomainHostRepository implements DomainHostRepositoryInterface
{
    private $infrastructure;

    public function __construct(RepositoryInterface $infrastructure)
    {
        $this->infrastructure = $infrastructure;
    }

    public function get(IdentityInterface $identity): DomainHost
    {
        return $this->infrastructure->get($identity);
    }

    public function add(DomainHost $domainHost): DomainHostRepositoryInterface
    {
        $this->infrastructure->add($domainHost);

        return $this;
    }

    public function remove(IdentityInterface $identity): DomainHostRepositoryInterface
    {
        $this->infrastructure->remove(
            $this->get($identity)
        );

        return $this;
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->infrastructure->has($identity);
    }

    public function count(): int
    {
        return $this->infrastructure->all()->size();
    }

    /**
     * {@inheritdoc}
     */
    public function all(): SetInterface
    {
        return $this
            ->infrastructure
            ->all()
            ->reduce(
                new Set(DomainHost::class),
                function(Set $all, DomainHost $domainHost): Set {
                    return $all->add($domainHost);
                }
            );
    }

    /**
     * @return SetInterface<DomainHost>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->infrastructure
            ->matching($specification)
            ->reduce(
                new Set(DomainHost::class),
                function(Set $all, DomainHost $domainHost): Set {
                    return $all->add($domainHost);
                }
            );
    }
}