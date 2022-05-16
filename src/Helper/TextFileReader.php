<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper;

use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;
use Generator;
use RuntimeException;

/**
 * Class TextFileReader
 * @package Bordeux\Bundle\GeoNameBundle\Helper
 */
class TextFileReader
{
    protected string $file;
    protected int $skipLines = 0;
    protected $progress;

    /**
     * @var Header[]
     */
    protected array $headers = [];

    /**
     * TextFileReader constructor.
     * @param string $file
     * @param callable|null $progress
     */
    public function __construct(string $file, ?callable $progress = null)
    {
        $this->file = $file;
        $this->progress = $progress;
    }

    /**
     * @param Header $header
     * @return $this
     */
    public function addHeader(Header $header): self
    {
        $this->headers[] = $header;
        return $this;
    }

    public function skipLines(int $lines): self
    {
        $this->skipLines = $lines;
        return $this;
    }

    /**
     * @param Header[] $headers
     * @return $this
     */
    public function addHeaders(array $headers): self
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
        return $this;
    }

    /**
     * @param callable|null $progress
     * @return $this
     */
    public function setProgress(?callable $progress): self
    {
        $this->progress = $progress;

        return $this;
    }


    /**
     * @param int $bulkSize
     * @return Generator
     */
    public function process(int $bulkSize): Generator
    {
        $fileSize = filesize($this->file);
        $handle = fopen($this->file, "rb");
        if (false === $handle) {
            throw new RuntimeException("Unable to open {$this->file}");
        }
        $readBytes = 0;
        $lineNumber = 0;
        $progress = $this->progress;

        $buffer = [];
        while (!feof($handle)) {
            $lineNumber++;
            $lineRaw = fgets($handle, 1024 * 1024);
            $readBytes += strlen($lineRaw);
            $line = trim($lineRaw);

            if (strlen($line) === 0) {
                continue;
            }

            if (strlen($line) === 0 || $line[0] === '#') {
                continue;
            }
            if ($lineNumber <= $this->skipLines) {
                continue;
            }

            $item = $this->convertToItem($line, $lineNumber);
            if ($item !== null) {
                $buffer[] = $item;
            }

            if (count($buffer) >= $bulkSize) {
                yield $buffer;
                $buffer = [];
                $progress && $progress($readBytes / $fileSize);
            }
        }

        if (count($buffer)) {
            yield $buffer;
        }
        $progress && $progress($readBytes / $fileSize);
        fclose($handle);
    }

    /**
     * @param string $line
     * @return mixed
     */
    protected function convertToItem(string $line, int $lineNumber): mixed
    {
        $tsv = explode("\t", $line);
        $result = [];
        foreach ($this->headers as $header) {
            $result[$header->getName()] = $header->getValue($tsv, $lineNumber);
        }
        return $result;
    }
}
