<?php

namespace Bordeux\Bundle\GeoNameBundle\Command;

use Bordeux\Bundle\GeoNameBundle\Helper\Downloader;
use Bordeux\Bundle\GeoNameBundle\Import\ImportInterface;
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

    /**
     * @var ImportInterface[]
     */
    protected array $importers;

    /**
     * ImportCommand constructor.
     * @param ImportInterface[] $importers
     */
    public function __construct(
        array $importers
    ) {
        $this->importers = $importers;
        parent::__construct(static::NAME);
    }

    /**
     * @return ImportInterface[]
     */
    public function getImporters(): array
    {
        return $this->importers;
    }

    /**
     *
     */
    protected function configure()
    {
        $commandLine = $this
            ->setName('bordeux:geoname:import')
            ->setDescription('Import GeoNames')
            ->addOption(
                'download-dir',
                'o',
                InputOption::VALUE_OPTIONAL,
                "Download dir",
                sys_get_temp_dir()
            );

        foreach ($this->importers as $importer) {
            $commandLine = $commandLine->addOption(
                $importer->getOptionName(),
                null,
                InputOption::VALUE_OPTIONAL,
                $importer->getDescription(),
                $importer->getDefaultValue()
            )->addOption(
                "skip-{$importer->getOptionName()}",
                null,
                InputOption::VALUE_OPTIONAL,
                "Skip importing {$importer->getName()}",
                false
            );
        }
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $downloadDir = $input->getOption('download-dir');

        foreach ($this->importers as $importer) {
            $value = $input->getOption($importer->getOptionName());
            $skip = $input->getOption("skip-" . $importer->getOptionName());
            if (empty($value) || $skip) {
                $output->writeln("\nSkipping importing {$importer->getName()}");
                continue;
            }

            $output->writeln("\nStart importing {$importer->getName()} from {$value}");
            $progress = $this->getProgressBar($output, "Downloading data for " . $importer->getName());
            $downloader = new Downloader($value, $downloadDir);
            $file = $downloader->start(function ($value) use ($progress) {
                $progress->setProgress($value);
            });
            $progress->finish();


            $progress = $this->getProgressBar($output, "Importing data: " . $importer->getName());
            $importer->import(
                $file,
                function ($value) use ($progress) {
                    $progress->setProgress($value);
                }
            )->wait();
            $progress->finish();
            $downloader = null;
            $output->writeln("\nFinished importing {$importer->getName()}");
        }

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param string $title
     * @return ProgressBar
     */
    private function getProgressBar(OutputInterface $output, string $title): ProgressBar
    {
        $progress = new ProgressBar($output, 100);
        $progress->setFormat(self::PROGRESS_FORMAT);
        $progress->setMessage($title);
        $progress->setRedrawFrequency(1);
        $progress->start();
        $progress->display();
        return $progress;
    }
}
