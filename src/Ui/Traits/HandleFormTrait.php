<?php

declare(strict_types=1);

namespace Randock\DddBundle\Ui\Traits;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Randock\Ddd\Validation\Exception\ValidationException;

trait HandleFormTrait
{
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
        $form->handleRequest($request);

        // check if it's valid
        if ($form->isSubmitted() && $form->isValid()) {
            $orderCommand = $form->getData();

            try {
                $result = $this->getCommandBus()->handle($orderCommand);
                if (null !== $successClosure) {
                    $successClosure->call($this, $result);
                }
            } catch (ValidationException $exception) {
                foreach ($exception->getErrors() as $field => $error) {
                    $form->get($field)->addError(
                        new FormError(
                            $error
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
