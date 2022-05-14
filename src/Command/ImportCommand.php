<?php

namespace Bordeux\Bundle\GeoNameBundle\Command;

use Bordeux\Bundle\GeoNameBundle\Import\AdministrativeImport;
use Bordeux\Bundle\GeoNameBundle\Import\AlternateNameImport;
use Bordeux\Bundle\GeoNameBundle\Import\CountryImport;
use Bordeux\Bundle\GeoNameBundle\Import\GeoNameImport;
use Bordeux\Bundle\GeoNameBundle\Import\HierarchyImport;
use Bordeux\Bundle\GeoNameBundle\Import\ImportInterface;
use Bordeux\Bundle\GeoNameBundle\Import\TimeZoneImport;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCommand
 * @package Bordeux\Bundle\GeoNameBundle\Command
 */
class ImportCommand extends Command
{
    const NAME = 'bordeux:geoname:import';
    const PROGRESS_FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% Mem: %memory:6s% %message%';

    protected string $cacheDir;
    protected AdministrativeImport $administrativeImport;
    protected AlternateNameImport $alternateNameImport;
    protected CountryImport $countryImport;
    protected GeoNameImport $geoNameImport;
    protected HierarchyImport $hierarchyImport;
    protected TimeZoneImport $timeZoneImport;

    /**
     * ImportCommand constructor.
     * @param string $cacheDir
     * @param AdministrativeImport $administrativeImport
     * @param AlternateNameImport $alternateNameImport
     * @param CountryImport $countryImport
     * @param GeoNameImport $geoNameImport
     * @param HierarchyImport $hierarchyImport
     * @param TimeZoneImport $timeZoneImport
     */
    public function __construct(
        string $cacheDir,
        AdministrativeImport $administrativeImport,
        AlternateNameImport $alternateNameImport,
        CountryImport $countryImport,
        GeoNameImport $geoNameImport,
        HierarchyImport $hierarchyImport,
        TimeZoneImport $timeZoneImport
    )
    {
        parent::__construct(static::NAME);
        $this->cacheDir = $cacheDir;
        $this->administrativeImport = $administrativeImport;
        $this->alternateNameImport = $alternateNameImport;
        $this->countryImport = $countryImport;
        $this->geoNameImport = $geoNameImport;
        $this->hierarchyImport = $hierarchyImport;
        $this->timeZoneImport = $timeZoneImport;
    }


    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('bordeux:geoname:import')
            ->addOption(
                'archive',
                'a',
                InputOption::VALUE_OPTIONAL,
                "Archive to GeoNames",
                'http://download.geonames.org/export/dump/allCountries.zip'
            )
            ->addOption(
                'timezones',
                't',
                InputOption::VALUE_OPTIONAL,
                "Timezones file",
                'http://download.geonames.org/export/dump/timeZones.txt'
            )
            ->addOption(
                'admin1-codes',
                'a1',
                InputOption::VALUE_OPTIONAL,
                "Admin 1 Codes file",
                'http://download.geonames.org/export/dump/admin1CodesASCII.txt'
            )
            ->addOption(
                'hierarchy',
                'hi',
                InputOption::VALUE_OPTIONAL,
                "Hierarchy ZIP file",
                'http://download.geonames.org/export/dump/hierarchy.zip'
            )
            ->addOption(
                'admin2-codes',
                'a2',
                InputOption::VALUE_OPTIONAL,
                "Admin 2 Codes file",
                'http://download.geonames.org/export/dump/admin2Codes.txt'
            )
            ->addOption(
                'languages-codes',
                'lc',
                InputOption::VALUE_OPTIONAL,
                "Admin 2 Codes file",
                'http://download.geonames.org/export/dump/iso-languagecodes.txt'
            )
            ->addOption(
                'country-info',
                'ci',
                InputOption::VALUE_OPTIONAL,
                "Country info file",
                'http://download.geonames.org/export/dump/countryInfo.txt'
            )
            ->addOption(
                'alternate-names',
                'an',
                InputOption::VALUE_OPTIONAL,
                "Alternate names file",
                'http://download.geonames.org/export/dump/alternateNamesV2.zip'
            )
            ->addOption(
                'download-dir',
                'o',
                InputOption::VALUE_OPTIONAL,
                "Download dir",
            )
            ->setDescription('Import GeoNames');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $downloadDir = $this->getDownloadDir($input);

        //timezones
        $timezones = $input->getOption('timezones');
        $timezonesLocal = $downloadDir . DIRECTORY_SEPARATOR . strtolower(basename($timezones));

        $this->downloadWithProgressBar(
            $timezones,
            $timezonesLocal,
            $output
        )->wait();
        $output->writeln('');


        // country-info
        $countryInfo = $input->getOption('country-info');
        $countryInfoLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($countryInfo);

        $this->downloadWithProgressBar(
            $countryInfo,
            $countryInfoLocal,
            $output
        )->wait();
        $output->writeln('');

        //importing
        $output->writeln('');

        $this->importWithProgressBar(
            $this->timeZoneImport,
            $timezonesLocal,
            "Importing timezones",
            $output
        )->wait();

        $output->writeln('');

        if (!$input->getOption("skip-admin1")) {
            // admin1
            $admin1 = $input->getOption('admin1-codes');
            $admin1Local = $downloadDir . DIRECTORY_SEPARATOR . basename($admin1);

            $this->downloadWithProgressBar(
                $admin1,
                $admin1Local,
                $output
            )->wait();
            $output->writeln('');

            $this->importWithProgressBar(
                $this->administrativeImport,
                $admin1Local,
                "Importing administrative 1",
                $output
            )->wait();

            $output->writeln('');
        }


        if (!$input->getOption("skip-admin2")) {
            $admin2 = $input->getOption('admin2-codes');
            $admin2Local = $downloadDir . DIRECTORY_SEPARATOR . basename($admin2);


            $this->downloadWithProgressBar(
                $admin2,
                $admin2Local,
                $output
            )->wait();
            $output->writeln('');

            $this->importWithProgressBar(
                $this->administrativeImport,
                $admin2Local,
                "Importing administrative 2",
                $output
            )->wait();


            $output->writeln('');
        }


        if (!$input->getOption("skip-geoname")) {
            // archive
            $archive = $input->getOption('archive');
            $archiveLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($archive);

            $this->downloadWithProgressBar(
                $archive,
                $archiveLocal,
                $output
            )->wait();
            $output->writeln('');

            $this->importWithProgressBar(
                $this->geoNameImport,
                $archiveLocal,
                "Importing GeoNames",
                $output,
                1000
            )->wait();


            $output->writeln("");
        }

        //countries import
        $this->importWithProgressBar(
            $this->countryImport,
            $countryInfoLocal,
            "Importing Countries",
            $output
        )->wait();


        if (!$input->getOption("skip-hierarchy")) {
            // archive
            $archive = $input->getOption('hierarchy');
            $archiveLocal = $downloadDir . DIRECTORY_SEPARATOR . basename($archive);

            $this->downloadWithProgressBar(
                $archive,
                $archiveLocal,
                $output
            )->wait();
            $output->writeln('');

            $this->importWithProgressBar(
                $this->hierarchyImport,
                $archiveLocal,
                "Importing Hierarchy",
                $output,
                1000
            )->wait();


            $output->writeln("");
        }


        $output->writeln("");


        $output->writeln("Imported successfully! Thank you :) ");

        return 0;
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
     * @return PromiseInterface
     */
    public function downloadWithProgressBar(string $url, string $saveAs, OutputInterface $output): PromiseInterface
    {
        if (file_exists($saveAs) && is_file($saveAs)) {
            $output->writeln($saveAs . " exists locally, skipping");
            $promise = new Promise();
            $promise->resolve("In cache!");
            return $promise;
        }

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
                'progress' => function ($downloadTotal, $downloadedBytes) use ($progress) {
                    if ($downloadTotal && is_callable($progress)) {
                        $progress($downloadedBytes / $downloadTotal);
                    }
                },
                'sink' => $saveAs,
                'save_to' => $saveAs, // support guzzle 6
            ]
        );

        return $promise;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getDownloadDir(InputInterface $input): string
    {
        $downloadDir = $input->getOption('download-dir') ?: $this->cacheDir . DIRECTORY_SEPARATOR . 'bordeux/geoname';
        !file_exists($downloadDir) && !mkdir($downloadDir, 0700, true) && !is_dir($downloadDir);
        return realpath($downloadDir);
    }
}
