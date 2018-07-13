<?php

declare(strict_types=1);

namespace Tests\Randock\DddBundle\Unit\Middleware;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Randock\DddBundle\Middleware\TranslationMiddleware;
use Randock\Ddd\Validation\Exception\ValidationException;
use Tests\Randock\DddBundle\Unit\Traits\TranslatorTestTrait;

class TranslationMiddlewareTest extends TestCase
{
    use TranslatorTestTrait;

    /**
     * @var TranslationMiddleware
     */
    private $translatorMiddleware;

    protected function setUp()
    {
        parent::setUp();
        $this->translatorMiddleware = new TranslationMiddleware($this->translator());
    }

    public function testExecuteWithException()
    {
        $command = null;
        $callable = function ($command) {
            $errors = [
                'name' => 'randock.validation.key',
            ];

            throw new ValidationException($errors);
        };
        $this->shouldCallTransOnTranslator('randock.validation.key', 'Key');

        try {
            $this->translatorMiddleware->execute($command, $callable);
        } catch (ValidationException $exception) {
            foreach ($exception->getErrors() as $field => $error) {
                $this->assertSame('name', $field);
                $this->assertSame('Key', $error);
            }
        }
    }

    public function testExecuteWithExceptionNested()
    {
        $command = null;
        $callable = function ($command) {
            $errors = [
                'name' => [
                    'file' => 'randock.validation.key',
                ],
            ];
            throw new ValidationException($errors);
        };
        $this->shouldCallTransOnTranslator('randock.validation.key', 'Key');

        try {
            $this->translatorMiddleware->execute($command, $callable);
        } catch (ValidationException $exception) {
            foreach ($exception->getErrors() as $field => $error) {
                $this->assertSame('name', $field);
                $this->assertSame(gettype([]), gettype($error));
                foreach ($error as $fieldChild => $errorChild) {
                    $this->assertSame('file', $fieldChild);
                    $this->assertSame('Key', $errorChild);
                }
            }
        }
    }
}
