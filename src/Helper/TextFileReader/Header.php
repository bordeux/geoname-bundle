<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

/**
 * Class Header
 * @package Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader
 */
class Header
{
    protected string $name;
    protected int $index;

    /**
     * Header constructor.
     * @param int $index
     * @param string $name
     */
    public function __construct(int $index, string $name)
    {
        $this->index = $index;
        $this->name = $name;
    }


    public function getValue(array $csvRow)
    {
        return $csvRow[$this->index] ?? null;
    }
}
