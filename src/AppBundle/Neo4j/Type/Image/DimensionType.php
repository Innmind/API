<?php
declare(strict_types = 1);

namespace AppBundle\Neo4j\Type\Image;

use Domain\Entity\Image\Dimension;
use Innmind\Neo4j\ONM\TypeInterface;
use Innmind\Immutable\{
    CollectionInterface,
    SetInterface,
    Set
};

final class DimensionType implements TypeInterface
{
    private static $identifiers;

    /**
     * {@inheritdoc}
     */
    public static function fromConfig(CollectionInterface $config): TypeInterface
    {
        return new self;
    }

    /**
     * {@inheritdoc}
     */
    public function forDatabase($value)
    {
        if ($value === null) {
            return;
        }

        return [$value->width(), $value->height()];
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return new Dimension((int) $value[0], (int) $value[1]);
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function identifiers(): SetInterface
    {
        if (self::$identifiers === null) {
            self::$identifiers = (new Set('string'))->add('image_dimension');
        }

        return self::$identifiers;
    }
}