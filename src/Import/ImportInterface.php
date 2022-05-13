<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use GuzzleHttp\Promise\PromiseInterface;

interface ImportInterface
{
    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return PromiseInterface
     */
    public function import(string $filePath, ?callable $progress = null): PromiseInterface;
}
