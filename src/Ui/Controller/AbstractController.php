<?php

declare(strict_types=1);

namespace Randock\DddBundle\Ui\Controller;

use League\Tactician\CommandBus;
use Randock\DddBundle\Ui\Traits\HandleFormTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller
{
    use HandleFormTrait;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * OrderController constructor.
     *
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @return CommandBus
     */
    public function getCommandBus(): CommandBus
    {
        return $this->commandBus;
    }
}
