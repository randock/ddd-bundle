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
            $command = $form->getData();

            try {
                $result = $this->getCommandBus()->handle($command);
                if (null !== $successClosure) {
                    $successClosure->call($this, $result);
                }
            } catch (ValidationException $exception) {
                $this->addFormErrors($form, $exception->getErrors());

                // errors
                if (null !== $errorClosure) {
                    $errorClosure->call($this, $exception);
                }
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param array         $errors
     */
    private function addFormErrors(FormInterface $form, array $errors)
    {
        foreach ($errors as $field => $error) {
            if (!\is_array($error)) {
                if ($form->offsetExists($field)) {
                    $form->get($field)->addError(
                        new FormError(
                            $error
                        )
                    );
                } else {
                    $form->addError(
                        new FormError(
                            $error
                        )
                    );
                }
            } else {
                $this->addFormErrors($form->get($field), $error);
            }
        }
    }
}
