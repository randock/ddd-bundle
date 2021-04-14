<?php

declare(strict_types=1);

namespace Randock\DddBundle\Middleware;

use League\Tactician\Middleware;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Randock\DddBundle\Middleware\Definition\CacheableInterface;

class CacheMiddleware implements Middleware
{
    /**
     * @var string
     */
    public const CACHE_PREFIX = 'commandBus_cache_';

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * CacheMiddleware constructor.
     *
     * @param CacheInterface|null $cache
     */
    public function __construct(?CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof CacheableInterface && $this->cache instanceof CacheInterface) {
            $cacheKey = sprintf(
                '%s%s',
                self::CACHE_PREFIX,
                $command->getCacheKey()
            );

            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($next, $command) {
                $item->expiresAfter($command->getCacheTtl());

                return $next($command);
            });
        }

        return $next($command);
    }
}
