<?php

declare(strict_types=1);

namespace Randock\DddBundle\Infrastructure\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class NullToEmptyDataTransformer.
 */
class NullToEmptyDataTransformer implements DataTransformerInterface
{
    /**
     * Does not transform anything.
     *
     * @param string|null $value
     *
     * @return string|null
     */
    public function transform($value): ?string
    {
        return $value;
    }

    /**
     * Transforms a null to an empty string.
     *
     * @param string|null $value
     *
     * @return string
     */
    public function reverseTransform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value;
    }
}
