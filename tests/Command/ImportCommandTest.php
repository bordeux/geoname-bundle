<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Command\ImportCommand;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
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
    }

    /**
     * @throws \Exception
     */
    public function testDownload(): void
    {
        $command = $this->application->find('bordeux:geoname:import');
        $command->setApplication($this->application);


        $input = new ArrayInput([
            'command' => $command->getName(),
            '--geonames' => 'https://download.geonames.org/export/dump/AX.zip'
        ]);

        $result = $command->run($input, $this->output);

        $this->assertEquals($result, 0);

        $geoNameRepo = self::$kernel->getContainer()
            ->get("doctrine")
            ->getRepository(GeoName::class);

        /** @var GeoName $ytterskaer */
        $ytterskaer = $geoNameRepo->find(630694);

        $this->assertInstanceOf(GeoName::class, $ytterskaer);

        $this->assertEquals($ytterskaer->getName(), 'Ytterskär');
        $this->assertEquals($ytterskaer->getAsciiName(), 'Ytterskaer');
        $this->assertEquals($ytterskaer->getCountryCode(), 'AX');

        $timezone = $ytterskaer->getTimezone();


        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertEquals($timezone->getTimezone(), 'Europe/Mariehamn');
    }
}
