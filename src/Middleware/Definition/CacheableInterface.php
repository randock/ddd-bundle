<?php

declare(strict_types=1);

namespace Randock\DddBundle\Middleware\Definition;

interface CacheableInterface
{
    /**
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * @return int
     */
    public function getCacheTtl(): int;
}
