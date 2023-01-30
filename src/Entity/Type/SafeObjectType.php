<?php

namespace JMS\JobQueueBundle\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

use function is_resource;
use function restore_error_handler;
use function serialize;
use function set_error_handler;
use function stream_get_contents;
use function unserialize;

class SafeObjectType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBlobTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return serialize($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        set_error_handler(function (int $code, string $message): bool {
            throw ConversionException::conversionFailedUnserialization($this->getName(), $message);
        });

        try {
            return unserialize($value);
        } finally {
            restore_error_handler();
        }
    }


    public function getName()
    {
        return 'jms_job_safe_object';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }


}
