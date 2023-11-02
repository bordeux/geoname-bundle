<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

use InvalidArgumentException;
use Throwable;

/**
 * Class Header
 * @package Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader
 */
class Header
{
    public const TYPE_STRING = 1;
    public const TYPE_INT = 2;
    public const TYPE_FLOAT = 3;
    public const TYPE_STRING_OR_NAN = 4;

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
         if ($this->type === static::TYPE_STRING_OR_NAN) { //Permit last inexistant fields
            if (!isset($row[$this->index]))
                return null;
            trim($row[$this->index]);
        }

        
        if (!isset($row[$this->index])) {
            throw $this->createException($row, $lineNumber, "Unable to find column `{$this->index}`");
        }

        $value = trim($row[$this->index]);
        if ("" === $value) {
            return null;
        }

        if ($this->type === static::TYPE_FLOAT) {
            if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                return (float)$value;
            }
            throw $this->createException($row, $lineNumber, "Unable cast `{$value}` to float");
        }

        if ($this->type === static::TYPE_INT) {
            if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                return (int)$value;
            }
            if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                return (int)round((float)$value);
            }

            throw $this->createException($row, $lineNumber, "Unable cast `{$value}` to int");
        }

        return $value;
    }

    protected function createException(array $row, int $lineNumber, string $message): Throwable
    {
        return new InvalidArgumentException("Column {$this->getName()}, Index: {$this->index}, TSV line: {$lineNumber}, Message: {$message}, Row: " . implode(",", $row));
    }
}
