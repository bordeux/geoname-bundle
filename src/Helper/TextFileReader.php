<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper;

use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;
use Generator;
use RuntimeException;

/**
 * Class Downloader
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
        while (!feof($handle)) {
            $lineNumber++;
            $line = fread($handle, 8192);
            $readBytes += strlen($line);
            if ($lineNumber <= $this->skipLines) {
                continue;
            }
        }
        fclose($handle);
    }
}
