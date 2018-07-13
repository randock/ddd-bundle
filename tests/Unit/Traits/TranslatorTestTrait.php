<?php

declare(strict_types=1);

namespace Tests\Randock\DddBundle\Unit\Traits;

use Symfony\Component\Translation\TranslatorInterface;

trait TranslatorTestTrait
{
    /**
     * @var \Mockery\MockInterface|TranslatorInterface
     */
    private $translator;

    /**
     * @return \Mockery\MockInterface|TranslatorInterface
     */
    protected function translator()
    {
        $this->translator = $this->translator ?? \Mockery::mock(TranslatorInterface::class);

        return $this->translator;
    }

    /**
     * @param string $key
     * @param string $message
     */
    protected function shouldCallTransOnTranslator(string $key, string $message)
    {
        $this->translator()
            ->shouldReceive('trans')
            ->withArgs([$key])
            ->andReturn($message);
    }
}
