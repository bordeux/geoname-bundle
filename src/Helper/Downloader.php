<?php

namespace Bordeux\Bundle\GeoNameBundle\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use RuntimeException;
use ZipArchive;

/**
 * Class Downloader
 * @package Bordeux\Bundle\GeoNameBundle\Helper
 */
class Downloader
{
    const TMP_PREFIX = "geonames_";
    const COPY_BUFFER = 1024 * 1024;

    protected ClientInterface $client;
    protected string $url;
    protected string $tmpDir;
    protected array $generatedFiles = [];

    /**
     * Downloader constructor.
     * @param string $url
     * @param string $tmpDir
     */
    public function __construct(string $url, string $tmpDir)
    {
        $this->url = $url;
        $this->tmpDir = $tmpDir;
        $this->client = new Client([]);
        $this->file = $this->getTempFile();
    }


    public function start(callable $progress): string
    {
        $uri = new Uri($this->url);
        $fragment = $uri->getFragment();
        $uri->withFragment('');
        $saveAs = $this->file;
        $percentageFactor = 1;
        if ($fragment) {
            $saveAs = $this->getTempFile();
            $percentageFactor = 0.8;
        }

        $this->client->getAsync(
            new Uri($this->url),
            [
                'progress' => function ($downloadTotal, $downloadedBytes) use ($progress, $percentageFactor) {
                    if ($downloadTotal) {
                        $progress(($downloadedBytes / $downloadTotal) * $percentageFactor);
                    }
                },
                'sink' => $saveAs,
                'save_to' => $saveAs, // support guzzle 6
            ]
        )->wait();

        if ($fragment) {
            $this->copyFromZip(
                $saveAs,
                $fragment,
                $this->file,
                fn($copied, $total) => ($progress($percentageFactor + ($copied / $total * (1 - $percentageFactor))))
            );
            unlink($saveAs);
        }

        return $this->file;
    }


    /**
     * @return string
     */
    private function getTempFile(): string
    {
        $file = tempnam($this->tmpDir, static::TMP_PREFIX);
        $this->generatedFiles[] = $file;
        return $file;
    }


    /**
     * @param string $zipFile
     * @param string $fileInZip
     * @param string $destination
     * @param callable $progress
     */
    private function copyFromZip(string $zipFile, string $fileInZip, string $destination, callable $progress): void
    {
        $zip = new ZipArchive();
        if (!$zip->open($zipFile)) {
            throw new RuntimeException("Unable to open {$zipFile} ZIP file");
        }

        $fileSize = 0;
        $stream = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if ($zip->getNameIndex($i) !== $fileInZip) {
                continue;
            }
            $fileSize = $zip->statIndex($i)['size'];
            $stream = $zip->getStream($fileInZip);
        }

        if (!$fileSize || !$stream) {
            throw new RuntimeException("Unable to open {$zipFile}#{$fileInZip} ZIP file");
        }

        $destinationFile = fopen($destination, 'wb');
        $readBytes = 0;
        while (!feof($stream)) {
            $buffer = fread($stream, static::COPY_BUFFER);
            fwrite($destinationFile, $buffer);
            $readBytes += strlen($buffer);
            $progress($readBytes, $fileSize);
        }
        fclose($stream);
        fclose($destinationFile);
        $zip->close();
    }


    /**
     *
     */
    public function __destruct()
    {
        foreach ($this->generatedFiles as $file) {
            @unlink($file);
        }
    }
}
