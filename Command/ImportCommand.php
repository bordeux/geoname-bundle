<?php

namespace Bordeux\Bundle\GeoNameBundle\Command;


use Bordeux\Bundle\GeoNameBundle\Import\ImportInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class VisitQueueCommand
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package TourRadar\Bundle\ApiBundle\Command\Queue
 */
class ImportCommand extends ContainerAwareCommand
{

    /**
     *
     */
    const PROGRESS_FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% Mem: %memory:6s% %message%';

    /**
     * Configuration method
     */
    protected function configure()
    {

        $this
            ->setName('bordeux:geoname:import')
            ->addOption('archive', 'a', InputOption::VALUE_OPTIONAL, "Archive to GeoNames", 'http://download.geonames.org/export/dump/allCountries.zip')
            ->addOption('timezones', 't', InputOption::VALUE_OPTIONAL, "Timezones file", 'http://download.geonames.org/export/dump/timeZones.txt')
            ->addOption('admin1-codes', 'a1', InputOption::VALUE_OPTIONAL, "Admin 1 Codes file", 'http://download.geonames.org/export/dump/admin1CodesASCII.txt')
            ->addOption('admin2-codes', 'a2', InputOption::VALUE_OPTIONAL, "Admin 2 Codes file", 'http://download.geonames.org/export/dump/admin2Codes.txt')
            ->addOption('languages-codes', 'lc', InputOption::VALUE_OPTIONAL, "Admin 2 Codes file", 'http://download.geonames.org/export/dump/iso-languagecodes.txt')
            ->addOption('download-dir', 'o', InputOption::VALUE_OPTIONAL, "Download dir", null)
            ->setDescription('Import GeoNames');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        ini_set('memory_limit','1024M');


        $downloadDir = $input->getOption('download-dir') ?: $this->getContainer()->getParameter("kernel.cache_dir") . DIRECTORY_SEPARATOR . 'bordeux/geoname';


        !file_exists($downloadDir) && mkdir($downloadDir, 0700, true);


        $downloadDir = realpath($downloadDir);

        // archive
        $archive = $input->getOption('archive');
        $archiveLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($archive);

        $this->downloadWithProgressBar(
            $archive,
            $archiveLocal,
            $output
        )->wait();
        $output->writeln('');

        //timezones
        $timezones = $input->getOption('timezones');
        $timezonesLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($timezones);

        $this->downloadWithProgressBar(
            $timezones,
            $timezonesLocal,
            $output
        )->wait();
        $output->writeln('');

        // admin1
        $admin1 = $input->getOption('admin1-codes');
        $admin1Local = $downloadDir . DIRECTORY_SEPARATOR . basename($admin1);

        $this->downloadWithProgressBar(
            $admin1,
            $admin1Local,
            $output
        )->wait();
        $output->writeln('');

        //admin2

        $admin2 = $input->getOption('admin2-codes');
        $admin2Local = $downloadDir . DIRECTORY_SEPARATOR . basename($admin2);


        $this->downloadWithProgressBar(
            $admin2,
            $admin2Local,
            $output
        )->wait();
        $output->writeln('');


        //importing

        $output->writeln('');

        $this->importWithProgressBar(
            $this->getContainer()->get("bordeux.geoname.import.timezone"),
            $timezonesLocal,
            "Importing timezones",
            $output
        )->wait();

        $output->writeln('');

        $this->importWithProgressBar(
            $this->getContainer()->get("bordeux.geoname.import.administrative"),
            $admin1Local,
            "Importing administrative 1",
            $output
        )->wait();


        $output->writeln('');

        $this->importWithProgressBar(
            $this->getContainer()->get("bordeux.geoname.import.administrative"),
            $admin2Local,
            "Importing administrative 2",
            $output
        )->wait();


        $output->writeln('');


        $archive = $input->getOption('archive');
        $archiveLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($archive);

        $this->importWithProgressBar(
            $this->getContainer()->get("bordeux.geoname.import.geoname"),
            $archiveLocal,
            "Importing GeoNames",
            $output,
            1000
        )->wait();


        $output->writeln("");
        $output->writeln("Imported successfully! Thank you :) ");

    }

    /**
     * @param ImportInterface $importer
     * @param string $file
     * @param string $message
     * @param OutputInterface $output
     * @param int $steps
     * @return \GuzzleHttp\Promise\Promise|\GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function importWithProgressBar(ImportInterface $importer, $file, $message, OutputInterface $output, $steps = 100)
    {
        $progress = new ProgressBar($output, $steps);
        $progress->setFormat(self::PROGRESS_FORMAT);
        $progress->setMessage($message);
        $progress->setRedrawFrequency(1);
        $progress->start();

        return $importer->import(
            $file,
            function ($percent) use ($progress, $steps) {
                $progress->setProgress((int)($percent * $steps));
            }
        )->then(function () use ($progress) {
            $progress->finish();
        });
    }


    /**
     * @param string $url
     * @param string $saveAs
     * @param OutputInterface $output
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function downloadWithProgressBar($url, $saveAs, OutputInterface $output)
    {
        $progress = new ProgressBar($output, 100);
        $progress->setFormat(self::PROGRESS_FORMAT);
        $progress->setMessage("Start downloading {$url}");
        $progress->setRedrawFrequency(1);
        $progress->start();

        return $this->download(
            $url,
            $saveAs,
            function ($percent) use ($progress) {
                $progress->setProgress((int)($percent * 100));
            }
        )->then(function () use ($progress) {
            $progress->finish();
        });

    }


    /**
     * @param string $url
     * @param string $output
     * @param callable $progress
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function download($url, $saveAs, callable $progress)
    {
        $client = new Client([]);

        $promise = $client->getAsync(
            new Uri($url),
            [
                'progress' => function ($totalSize, $downloadedSize) use ($progress) {
                    $totalSize && is_callable($progress) && $progress($downloadedSize / $totalSize);
                },
                'save_to' => $saveAs
            ]
        );

        return $promise;
    }
}
