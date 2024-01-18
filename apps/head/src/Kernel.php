<?php

declare(strict_types=1);

namespace App\Apps\Head;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function dirname;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const ROOT_PATH = '/../..';
    private const APP_PATH = '/head';

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . self::ROOT_PATH . '/var/cache' . self::APP_PATH . '/' . $this->environment;
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . self::ROOT_PATH . '/var/log' . self::APP_PATH;
    }

    /**
     * Gets the path to the configuration directory.
     */
    private function getConfigDir(): string
    {
        return $this->getProjectDir() . '/config';
    }
}
