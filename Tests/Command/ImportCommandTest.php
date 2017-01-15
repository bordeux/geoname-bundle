<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ImportCommandTest extends WebTestCase
{

    public function testExecute()
    {
        self::bootKernel();

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
