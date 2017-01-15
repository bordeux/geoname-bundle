<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;


/**
 * Class ImportCommandTest
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Tests\Command
 */
class ImportCommandTest extends CommandTestCase
{


    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function testExecute()
    {
        self::bootKernel();

        $client = self::createClient();
        $stream = $this->runCommand($client, "bordeux:geoname:import --archive http://download.geonames.org/export/dump/PL.zip'");

        $output = '';
        foreach ($stream as $line){
            $output .= $line.PHP_EOL;
            echo $line;
        }

        $this->assertContains('Imported successfully', $output);

        $this->testContent();

    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function testContent(){
        $geoNameRepo = self::$kernel->getContainer()
            ->get("doctrine")
            ->getRepository("BordeuxGeoNameBundle:GeoName");

        /** @var GeoName $chorzow */
        $chorzow = $geoNameRepo->find(3101619);

        $this->assertInstanceOf(GeoName::class, $chorzow);

        $this->assertEquals($chorzow->getName(), 'Chorzów');
        $this->assertEquals($chorzow->getAsciiName(), 'Chorzow');
        $this->assertEquals($chorzow->getCountryCode(), 'PL');

        $timezone = $chorzow->getTimezone();


        $this->assertInstanceOf(Timezone::class, $timezone);
        $this->assertEquals($timezone->getTimezone(), 'Europe/Warsaw');

    }
}
