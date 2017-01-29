<?php
declare(strict_types = 1);

namespace Domain\Specification\CitationAppearance;

use Domain\Entity\{
    CitationAppearance,
    HttpResource\IdentityInterface
};
use Innmind\Specification\ComparatorInterface;

final class HttpResource implements ComparatorInterface, SpecificationInterface
{
    use Composable;

    private $value;

    public function __construct(IdentityInterface $value)
    {
        $this->value = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function property(): string
    {
        return 'resource';
    }

    /**
     * {@inheritdoc}
     */
    public function sign(): string
    {
        return '=';
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    public function isSatisfiedBy(CitationAppearance $appearance): bool
    {
        return (string) $appearance->resource() === $this->value;
    }
}