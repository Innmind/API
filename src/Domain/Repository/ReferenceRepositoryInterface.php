<?php
declare(strict_types = 1);

namespace Domain\Repository;

use Domain\Entity\{
    Reference\IdentityInterface,
    Reference
};
use Innmind\Immutable\SetInterface;
use Innmind\Specification\SpecificationInterface;

interface ReferenceRepositoryInterface
{
    public function get(IdentityInterface $identity): Reference;
    public function add(Reference $reference): self;
    public function remove(IdentityInterface $identity): self;
    public function has(IdentityInterface $identity): bool;
    public function count(): int;

    /**
     * @return SetInterface<Reference>
     */
    public function all(): SetInterface;

    /**
     * @return SetInterface<Reference>
     */
    public function matching(SpecificationInterface $specification): SetInterface;
}
