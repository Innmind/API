<?php
declare(strict_types = 1);

namespace App\Repository\Neo4j;

use Domain\{
    Repository\HtmlPageRepository as HtmlPageRepositoryInterface,
    Entity\HtmlPage,
    Entity\HtmlPage\Identity,
    Exception\HtmlPageNotFound,
    Specification\HttpResource\Specification
};
use Innmind\Neo4j\ONM\{
    Repository,
    Exception\EntityNotFound
};
use Innmind\Immutable\Set;

final class HtmlPageRepository implements HtmlPageRepositoryInterface
{
    private Repository $infrastructure;

    public function __construct(Repository $infrastructure)
    {
        $this->infrastructure = $infrastructure;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    public function get(Identity $identity): HtmlPage
    {
        try {
            /**
             * @psalm-suppress InvalidArgument
             * @psalm-suppress LessSpecificReturnStatement
             */
            return $this->infrastructure->get($identity);
        } catch (EntityNotFound $e) {
            throw new HtmlPageNotFound('', 0, $e);
        }
    }

    public function add(HtmlPage $page): HtmlPageRepositoryInterface
    {
        $this->infrastructure->add($page);

        return $this;
    }

    public function remove(Identity $identity): HtmlPageRepositoryInterface
    {
        $this->infrastructure->remove(
            $this->get($identity)
        );

        return $this;
    }

    public function has(Identity $identity): bool
    {
        /** @psalm-suppress InvalidArgument */
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
            ->toSetOf(HtmlPage::class);
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Specification $specification): Set
    {
        return $this
            ->infrastructure
            ->matching($specification)
            ->toSetOf(HtmlPage::class);
    }
}
