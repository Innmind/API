<?php
declare(strict_types = 1);

namespace Domain\Specification\Canonical;

use Domain\Entity\{
    Canonical as Entity,
    HttpResource\Identity,
};
use Innmind\Specification\{
    Comparator,
    Sign,
};

final class Canonical implements Comparator, Specification
{
    use Composable;

    private $value;

    public function __construct(Identity $value)
    {
        $this->value = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function property(): string
    {
        return 'canonical';
    }

    /**
     * {@inheritdoc}
     */
    public function sign(): Sign
    {
        return Sign::equality();
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    public function isSatisfiedBy(Entity $canonical): bool
    {
        return (string) $canonical->canonical() === $this->value;
    }
}
