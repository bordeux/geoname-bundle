<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        date_default_timezone_set('UTC');
        parent::boot();
    }


    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
