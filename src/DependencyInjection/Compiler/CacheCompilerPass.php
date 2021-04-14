<?php

declare(strict_types=1);

namespace Randock\DddBundle\DependencyInjection\Compiler;

use Symfony\Contracts\Cache\CacheInterface;
use Randock\DddBundle\Middleware\CacheMiddleware;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Randock\DddBundle\DependencyInjection\Exception\InvalidCacheServiceException;

class CacheCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws InvalidCacheServiceException
     * @throws \ReflectionException
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasAlias('randock_ddd.command_cache_service')) {
            try {
                /** @var ChildDefinition $cacheDefinition */
                $cacheDefinition = $container->findDefinition('randock_ddd.command_cache_service');
                $parentDefinition = $container->findDefinition($cacheDefinition->getParent());

                /** @var class-string $cacheClass */
                $cacheClass = $parentDefinition->getClass();

                $reflectionClass = new \ReflectionClass($cacheClass);
                if (!$reflectionClass->implementsInterface(CacheInterface::class)) {
                    throw new InvalidCacheServiceException(\sprintf("The service '%s' does not implements %s interface", $cacheClass, CacheInterface::class));
                }
            } catch (ServiceNotFoundException $exception) {
                throw new InvalidCacheServiceException(\sprintf("The service '%s' does not exists", $exception->getId()));
            }
        } else {
            $cacheDefinition = $container->findDefinition(CacheMiddleware::class);
            $cacheDefinition->replaceArgument(0, null);
        }
    }
}
