<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

use InvalidArgumentException;

/**
 * Class Header
 * @package Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader
 */
class Header
{
    public const TYPE_STRING = 1;
    public const TYPE_INT = 2;
    public const TYPE_FLOAT = 3;

    protected string $name;
    protected int $index;
    protected int $type;

    /**
     * Header constructor.
     * @param int $index
     * @param string $name
     * @param int $type
     */
    public function __construct(int $index, string $name, int $type = self::TYPE_STRING)
    {
        $this->index = $index;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param array $row
     * @param int $lineNumber
     * @return mixed
     */
    public function getValue(array $row, int $lineNumber): mixed
    {
        if (!isset($row[$this->index])) {
            throw new InvalidArgumentException("{$this->getName()}: Unable to find column `{$this->index}`. Issue on line {$lineNumber}");
        }

        $value = trim($row[$this->index]);
        if ("" === $value) {
            return null;
        }

        if ($this->type === static::TYPE_FLOAT) {
            if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                return (float)$value;
            }
            throw new InvalidArgumentException("{$this->getName()}: Unable cast `{$value}` to float. Issue on line {$lineNumber}");
        }

        if ($this->type === static::TYPE_INT) {
            if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                return (int)$value;
            }
            if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                return (int)round((float)$value);
            }

            throw new InvalidArgumentException("{$this->getName()}: Unable cast `{$value}` to int. Issue on line {$lineNumber}");
        }

        return $value;
    }
}
