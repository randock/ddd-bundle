<?php

declare(strict_types=1);

namespace Randock\DddBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Randock\DddBundle\DependencyInjection\Compiler\CacheCompilerPass;

/**
 * Class RandockDddBundle.
 */
class RandockDddBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CacheCompilerPass());
    }
}
