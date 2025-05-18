<?php

declare(strict_types=1);

namespace App\Apps\API;

use App\Apps\Shared\Kernel as AppKernel;

class Kernel extends AppKernel
{
    protected const string ROOT_PATH = '/../..';
    protected const string APP_PATH = 'api';

    #[\Override]
    public function getRootPath(): string
    {
        return self::ROOT_PATH;
    }

    #[\Override]
    public function getAppPath(): string
    {
        return self::APP_PATH;
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }
}
