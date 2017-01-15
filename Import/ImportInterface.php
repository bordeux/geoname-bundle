<?php


namespace Bordeux\Bundle\GeoNameBundle\Import;


use GuzzleHttp\Promise\Promise;
use SplFileObject;

interface ImportInterface
{

    /**
     * @param  string $filePath
     * @param callable|null $progress
     * @return Promise|\GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function import($filePath, callable $progress = null);


}
