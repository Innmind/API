<?php
declare(strict_types = 1);

namespace AppBundle\Neo4j\Type;

use Domain\Model\Language;
use Innmind\Neo4j\ONM\TypeInterface;
use Innmind\Immutable\{
    CollectionInterface,
    SetInterface,
    Set
};

final class LanguageType implements TypeInterface
{
    private static $identifiers;
    private $nullable = false;

    /**
     * {@inheritdoc}
     */
    public static function fromConfig(CollectionInterface $config): TypeInterface
    {
        $self = new self;

        if ($config->hasKey('nullable')) {
            $self->nullable = true;
        }

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function forDatabase($value)
    {
        if ($this->isNullable() && $value === null) {
            return;
        }

        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDatabase($value)
    {
        return new Language((string) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * {@inheritdoc}
     */
    public static function identifiers(): SetInterface
    {
        if (self::$identifiers === null) {
            self::$identifiers = (new Set('string'))->add('language');
        }

        return self::$identifiers;
    }
}