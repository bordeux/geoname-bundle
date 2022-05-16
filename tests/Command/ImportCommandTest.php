<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Command\ImportCommand;
use Bordeux\Bundle\GeoNameBundle\Entity\AlternateName;
use Bordeux\Bundle\GeoNameBundle\Entity\Country;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class ImportCommandTest
 * @package Bordeux\Bundle\GeoNameBundle\Tests\Command
 */
class ImportCommandTest extends KernelTestCase
{
    private const DOCTRINE_COMMAND = "doctrine:schema:update";

    protected $application;

    protected $output;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new Application(static::$kernel);
        $this->application->getHelp();

        $command = $this->application->find(static::DOCTRINE_COMMAND);
        $command->setApplication($this->application);

        $this->output = new StreamOutput(fopen('php://stdout', 'wb', false));
        ;
        $command->run(new ArrayInput([
            'command' => static::DOCTRINE_COMMAND,
            '--force' => true
        ]), $this->output);

        /** @var Registry $registry */
        $registry = self::$kernel->getContainer()->get("doctrine");
        $this->em = $registry->getManager();
    }

    /**
     *
     */
    public function testDownload(): void
    {
        /** @var ImportCommand $command */
        $command = $this->application->find('bordeux:geoname:import');
        $command->setApplication($this->application);

        $inputArray = [
            'command' => $command->getName(),
        ];
        foreach ($command->getImporters() as $importer) {
            $value = $importer->getTestValue();
            if (null === $value) {
                $inputArray['--skip-' . $importer->getOptionName()] = 1;
            } else {
                $inputArray['--' . $importer->getOptionName()] = $importer->getTestValue();
            }
        }

        $input = new ArrayInput($inputArray);
        $result = $command->run($input, $this->output);

        $this->assertEquals($result, 0);
    }


    /**
     * @depends testDownload
     */
    public function testContent(): void
    {
        /** @var Country $country */
        $country = $this->em->getRepository(Country::class)
            ->find(798544);

        self::assertSame("Poland", $country->getName());
        self::assertSame("PL", $country->getFips());
        self::assertSame("POL", $country->getIso3());
        self::assertSame("PL", $country->getIso());
        self::assertSame(616, $country->getIsoNumeric());
        self::assertSame("Warsaw", $country->getCapital());
        self::assertSame(".pl", $country->getTld());
        self::assertSame("PLN", $country->getCurrency());
        self::assertSame("Zloty", $country->getCurrencyName());
        self::assertSame(48, $country->getPhonePrefix());
        self::assertIsArray($country->getLanguages());
        self::assertContains("pl", $country->getLanguages());

        $geoName = $country->getGeoName();
        self::assertInstanceOf(GeoName::class, $geoName);
        self::assertSame(798544, $geoName->getId());
        self::assertSame("Republic of Poland", $geoName->getName());
        self::assertSame("PCLI", $geoName->getFeatureCode());
        self::assertSame("A", $geoName->getFeatureClass());
        self::assertSame("PL", $geoName->getCountryCode());
        self::assertGreaterThan(30_000_000, $geoName->getPopulation());
        self::assertLessThan(45_000_000, $geoName->getPopulation());


        $timezone = $geoName->getTimezone();
        self::assertInstanceOf(Timezone::class, $timezone);
        self::assertSame(372, $timezone->getId());
        self::assertSame("Europe/Warsaw", $timezone->getTimezone());
        self::assertSame("PL", $timezone->getCountryCode());
        self::assertSame(1, $timezone->getGmtOffset());
        self::assertSame(2, $timezone->getDstOffset());
        self::assertSame(1, $timezone->getRawOffset());

        $alternateNames = $geoName->getAlternateNames();
        /** @var AlternateName<string> $map */
        $map = [];
        foreach ($alternateNames as $item) {
            $map[$item->getType()] = $item;
        }

        self::assertSame('Polsha', $map['uz']->getValue());
        self::assertSame('Polonia', $map['sq']->getValue());
        self::assertSame('Polen', $map['no']->getValue());
        self::assertSame('Polen', $map['de']->getValue());
    }
}
