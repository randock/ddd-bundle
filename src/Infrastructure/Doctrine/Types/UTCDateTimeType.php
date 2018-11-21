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

        if(null === self::$utc){
            self::$utc = new \DateTimeZone('UTC');
        }

        $value->setTimezone(self::$utc);

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

        if(null === self::$utc){
            self::$utc = new \DateTimeZone('UTC');
        }

        $val = \DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::$utc
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
