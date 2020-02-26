<?php

declare(strict_types=1);

namespace Randock\DddBundle\Middleware;

use League\Tactician\Middleware;
use Psr\SimpleCache\CacheInterface;
use Randock\DddBundle\Middleware\Definition\CacheableInterface;

class CacheMiddleware implements Middleware
{
    /**
     * @var string
     */
    public const CACHE_PREFIX = 'commandBus_cache_';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * CacheMiddleware constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof CacheableInterface) {
            // check if it's cached
            $cacheKey = sprintf(
                '%s%s',
                self::CACHE_PREFIX,
                $command->getCacheKey()
            );

            if ($this->cache->has($cacheKey)) {
                return $this->cache->get($cacheKey);
            }

            $result = $next($command);
            $this->cache->set($cacheKey, $result, $command->getCacheTtl());

            return $result;
        }

        return $next($command);
    }
}
