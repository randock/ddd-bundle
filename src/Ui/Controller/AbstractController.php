<?php

declare(strict_types=1);

namespace Randock\DddBundle\Ui\Controller;

use League\Tactician\CommandBus;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Randock\Ddd\Validation\ValidationError;
use Symfony\Component\HttpFoundation\Request;
use Randock\Ddd\Validation\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller
{
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
     * @param Request       $request
     * @param FormInterface $form
     * @param \Closure|null $successClosure
     * @param \Closure|null $errorClosure
     */
    protected function handleForm(
        Request $request,
        FormInterface $form,
        \Closure $successClosure = null,
        \Closure $errorClosure = null
    ) {
        // handle the request
        $form->handleRequest($request);

        // check if it's a post and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $orderCommand = $form->getData();

            try {
                $result = $this->commandBus->handle($orderCommand);
                if (null !== $successClosure) {
                    $successClosure->call($this, $result);
                }
            } catch (ValidationException $exception) {
                /** @var ValidationError $error */
                foreach ($exception->getErrors() as $error) {
                    $form->get($error->getField())->addError(
                        new FormError(
                            $error->getMessage()
                        )
                    );
                }

                // errors
                if (null !== $errorClosure) {
                    $errorClosure->call($this, $exception);
                }
            }
        }
    }
}
