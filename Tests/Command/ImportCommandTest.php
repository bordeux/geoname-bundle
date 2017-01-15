<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Command\ImportCommand;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ImportCommandTest extends WebTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testDownload()
    {
        $application = new Application(static::$kernel);
        $application->add(new ImportCommand());

        $command = $application->find('bordeux:geoname:import');
        $command->setApplication($application);


        $input = new ArrayInput([
            'command' => $command->getName(),
            '--archive' => 'http://download.geonames.org/export/dump/AX.zip'
        ]);

        $output = new StreamOutput(fopen('php://stdout', 'w', false));;

        $result = $command->run($input, $output);


        $this->assertEquals((int) $result, 0);


        $geoNameRepo = self::$kernel->getContainer()
            ->get("doctrine")
            ->getRepository("BordeuxGeoNameBundle:GeoName");

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
