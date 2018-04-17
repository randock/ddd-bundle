<?php

declare(strict_types=1);

namespace Randock\DddExample\Infrastructure\Shared\Middleware;

use League\Tactician\Middleware;
use Symfony\Component\Translation\TranslatorInterface;
use Randock\Ddd\Validation\Exception\ValidationException;

class TranslationMiddleware implements Middleware
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TranslationMiddleware constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
            foreach ($exception->getErrors() as $error) {
                $error->setMessage(
                    $this->translator->trans(
                        $error->getMessage()
                    )
                );
            }

            throw $exception;
        }
    }
}
