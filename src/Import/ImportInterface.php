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


    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getOptionName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;


    /**
     * @return string
     */
    public function getDefaultValue(): string;


    /**
     * @return string|null
     */
    public function getTestValue(): ?string;
}
