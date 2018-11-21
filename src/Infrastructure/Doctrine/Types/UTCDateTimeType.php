<?php

declare(strict_types=1);

namespace Randock\DddBundle\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class UTCDateTimeType extends DateTimeType
{
    /** @var \DateTimeZone */
    private static $utc = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        
        if ($value instanceof \DateTime) {
            $value = \DateTimeImmutable::createFromMutable($value);
        }

        if (!$value instanceof \DateTimeImmutable) {
            return null;
        }
        $value->setTimezone((self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC')));

        return $value->format($platform->getDateTimeFormatString());
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        $val = \DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC'))
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
