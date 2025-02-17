<?php

declare(strict_types=1);

namespace App\Shared\Domain\BoundedContext;

use InvalidArgumentException;
use ReflectionObject;

final class Metadata
{
    private ReflectionObject $reflection;

    public function __construct(object $module)
    {
        $this->reflection = new ReflectionObject($module);
    }

    public function getNamespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function getRootDir(): string
    {
        $fileName = $this->reflection->getFileName();
        if ($fileName === false) {
            throw new InvalidArgumentException(sprintf('Invalid module: %%s%s', $this->reflection->getName()));
        }

        return dirname($fileName);
    }

    public function getClass(string $name): string
    {
        return sprintf('%s\\%s', $this->getNamespace(), $name);
    }

    public function getRelativePath(string $path): string
    {
        return sprintf('%s/%s', $this->getRootDir(), $path);
    }
}
