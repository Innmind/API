<?php
declare(strict_types = 1);

namespace Domain\Entity\Image;

use Domain\Exception\DomainException;

final class Dimension
{
    private int $height;
    private int $width;
    private string $string;

    public function __construct(int $width, int $height)
    {
        if ($height < 0 || $width < 0) {
            throw new DomainException;
        }

        $this->height = $height;
        $this->width = $width;
        $this->string = sprintf('%sx%s', $width, $height);
    }

    public function height(): int
    {
        return $this->height;
    }

    public function width(): int
    {
        return $this->width;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
