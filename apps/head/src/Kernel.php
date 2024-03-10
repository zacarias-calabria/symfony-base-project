<?php

declare(strict_types=1);

namespace App\Apps\Head;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const string ROOT_PATH = '/../..';
    private const string APP_PATH = '/head';

    #[\Override]
    public function getCacheDir(): string
    {
        return $this->getProjectDir() . self::ROOT_PATH . '/var/cache' . self::APP_PATH . '/' . $this->environment;
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    #[\Override]
    public function getLogDir(): string
    {
        return $this->getProjectDir() . self::ROOT_PATH . '/var/log' . self::APP_PATH;
    }
}
