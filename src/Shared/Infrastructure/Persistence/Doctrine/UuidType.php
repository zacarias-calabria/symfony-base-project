<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Utils\Strings;
use App\Shared\Domain\ValueObject\Uuid;
use App\Shared\Infrastructure\Doctrine\Dbal\DoctrineCustomType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

use function Lambdish\Phunctional\last;

abstract class UuidType extends StringType implements DoctrineCustomType
{
    public function getName(): string
    {
        return self::customTypeName();
    }

    public static function customTypeName(): string
    {
        return Strings::toSnakeCase(str_replace('Type', '', (string)last(explode('\\', static::class))));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        $className = $this->typeClassName();

        return new $className($value);
    }

    abstract protected function typeClassName(): string;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        /** @var Uuid $value */
        return $value->value();
    }
}
