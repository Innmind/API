<?php
declare(strict_types = 1);

namespace Domain\Repository;

use Domain\Entity\{
    HostResource\IdentityInterface,
    HostResource
};
use Innmind\Immutable\SetInterface;
use Innmind\Specification\SpecificationInterface;

interface HostResourceRepositoryInterface
{
    public function get(IdentityInterface $identity): HostResource;
    public function add(HostResource $hostResource): self;
    public function remove(IdentityInterface $identity): self;
    public function has(IdentityInterface $identity): bool;
    public function count(): int;

    /**
     * @return SetInterface<HostResource>
     */
    public function all(): SetInterface;

    /**
     * @return SetInterface<HostResource>
     */
    public function matching(SpecificationInterface $specification): SetInterface;
}
