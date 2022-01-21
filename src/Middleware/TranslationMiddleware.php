<?php

declare(strict_types=1);

namespace Randock\DddBundle\Middleware;

use League\Tactician\Middleware;
use Symfony\Contracts\Translation\TranslatorInterface;
use Randock\Ddd\Validation\Exception\ValidationException;

class TranslationMiddleware implements Middleware
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $domain;

    /**
     * TranslationMiddleware constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $domain
     */
    public function __construct(TranslatorInterface $translator, string $domain)
    {
        $this->translator = $translator;
        $this->domain = $domain;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @throws ValidationException
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        try {
            return $next($command);
        } catch (ValidationException $exception) {
            $errors = $this->translate($exception->getErrors());
            $exception->setErrors($errors);

            throw $exception;
        }
    }

    /**
     * @param array $errors
     *
     * @return array
     */
    private function translate(array $errors): array
    {
        foreach ($errors as $field => $error) {
            if (\is_array($error)) {
                $errors[$field] = $this->translate($error);
            } else {
                $errors[$field] = $this->translator->trans($error, [], $this->domain);
            }
        }

        return $errors;
    }
}
